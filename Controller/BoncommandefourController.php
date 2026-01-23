<?php

namespace App\Controller;

use App\Entity\Boncommandefour;
use App\Form\BoncommandefourType;
use App\Repository\BoncommandefourRepository;
use App\Repository\FournisseurRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

#[Route('/boncommandefour')]
final class BoncommandefourController extends AbstractController
{
    #[Route(name: 'app_boncommandefour_index', methods: ['GET'])]
    public function index(BoncommandefourRepository $boncommandefourRepository): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }

        $entreprise = $this->getUser()->getEntreprise()->getId();
        $bondecommandefour = $boncommandefourRepository->findBy(['identreprise' => $entreprise, 'deletedAt' => NULL]);
        return $this->render('boncommandefour/index.html.twig', [
            'boncommandefours' => $bondecommandefour,
        ]);
    }

        #[Route('/lister', name: 'bdcfour_lister', methods: ['GET'])]
    public function getListebdccli(Request $request, BoncommandefourRepository $boncommandefourRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $limit   = $request->query->get("length");
        $offset  = $request->query->get("start");
        $draw    = $request->query->get("draw");
        $searchValue    = $request->query->all("search");
        $search = $searchValue["value"];
        $entreprise = 0;
        $entreprise = $this->getUser()->getEntreprise()->getId();
        // Récupération des variables GET


        $data['recordsTotal']=0;
        $data['recordsFiltered']=0;
        $data['data']=[];
        $data['draw']=$draw + 1;
        $critere = [];
        if($search){
            $critere['search'] = $search;

        }else{
            $critere = [];
        }

        $liste = $boncommandefourRepository->getListebdcfour($critere, $entreprise, 'DESC',$limit,$offset);
        $countliste = $boncommandefourRepository->getListebdcfour($critere, $entreprise, 'DESC');
        $data['recordsTotal']= count($liste);
        $data['recordsFiltered'] = count($countliste);
        //Serialisation date 
        $dateCallback = function ($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []) {
        return $innerObject instanceof \DateTime ? $innerObject->format('d-m-Y') : '';
        };
        //Serialisation
        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer([$normalizer]);
        $data["data"] = $serializer->normalize($liste, null, 
            [
                AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,
                AbstractNormalizer::GROUPS => "show:liste",
                AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT => 2,
                AbstractObjectNormalizer::PRESERVE_EMPTY_OBJECTS,
                AbstractObjectNormalizer::SKIP_NULL_VALUES,
                AbstractNormalizer::CALLBACKS => [
                    'datebdc' => $dateCallback

                ],
            ]); 
        return $this->json($data,200, ["Content-Type" => "application/json"]);
    }

    #[Route('/new', name: 'app_boncommandefour_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, FournisseurRepository $fournisseurRepository): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();
        $identreprise = $user->getEntreprise()->getId();
        $bdcfour = $fournisseurRepository->findBy(['identreprise' => $identreprise, "deletedAt" => NULL]);
        $boncommandefour = new Boncommandefour();
        $form = $this->createForm(BoncommandefourType::class, $boncommandefour, ['fournisseur' => $bdcfour]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $brochureFile = $form->get('brochure')->getData();
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('bdccli_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $boncommandefour->setBrochure($newFilename);
            }
            //$entreprise = $user->getEntreprise();
            $boncommandefour->setIdentreprise($identreprise);
            $boncommandefour->setCreatedBy($this->getUser()->getId());
            $boncommandefour->setCreatedAt(new DateTime('now'));
            $entityManager->persist($boncommandefour);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_boncommandefour_new' : 'app_boncommandefour_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('boncommandefour/new.html.twig', [
            'boncommandefour' => $boncommandefour,
            'form' => $form,
            'response' => $response,
        ], $response);
    }

    #[Route('/{id}', name: 'app_boncommandefour_show', methods: ['GET'])]
    public function show(Boncommandefour $boncommandefour): Response
    {
        return $this->render('boncommandefour/show.html.twig', [
            'boncommandefour' => $boncommandefour,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_boncommandefour_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Boncommandefour $boncommandefour, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(BoncommandefourType::class, $boncommandefour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('brochure')->getData();

            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('bdccli_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $boncommandefour->setBrochure($newFilename);
            }
            $boncommandefour->setUpdatedAt(new \DateTime("now"));
            $boncommandefour->setUpdatedBy($this->getUser()->getId());
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_boncommandefour_new' : 'app_boncommandefour_index';
            if ($nextAction) {
                $this->addFlash('success', 'Modification avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('boncommandefour/edit.html.twig', [
            'boncommandefour' => $boncommandefour,
            'form' => $form,
            'response' => $response,
        ], $response);
    }

    #[Route('/delete', name: 'app_boncommandefour_delete', methods: ['POST'])]
    public function delete(Request $request, BoncommandefourRepository $boncommandefourRepository, EntityManagerInterface $entityManager): Response
    {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $id = $request->request->get('id-delete');
            $liste = $boncommandefourRepository->find($id);
            $liste->setDeletedAt(new \DateTime("now"));
            $liste->setDeletedBy($this->getUser()->getId());
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Suppression effectuée avec succès'
            );

        return $this->redirectToRoute('app_boncommandefour_index', [], Response::HTTP_SEE_OTHER);
    }

}
