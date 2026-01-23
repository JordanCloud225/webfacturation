<?php

namespace App\Controller;

use App\Entity\Boncommandeclient;
use App\Form\BoncommandeclientType;
use App\Repository\BoncommandeclientRepository;
use App\Repository\BoncommandeRepository;
use App\Repository\ClientRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

#[Route('/boncommandeclient')]
final class BoncommandeclientController extends AbstractController
{
    #[Route(name: 'app_boncommandeclient_index', methods: ['GET'])]
    public function index(BoncommandeclientRepository $boncommandeclientRepository): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }

        $entreprise = $this->getUser()->getEntreprise()->getId();
        $bondecommandeclient = $boncommandeclientRepository->findBy(['identreprise' => $entreprise, 'deletedAt' => NULL]);
        return $this->render('boncommandeclient/index.html.twig', [
            'boncommandeclients' => $bondecommandeclient,
        ]);
    }

     #[Route('/lister', name: 'bdccli_lister', methods: ['GET'])]
    public function getListebdccli(Request $request, BoncommandeclientRepository $boncommandeclientRepository): Response
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
 
        $liste = $boncommandeclientRepository->getListebdccli($critere, $entreprise, 'DESC',$limit,$offset);
        $countliste = $boncommandeclientRepository->getListebdccli($critere, $entreprise, 'DESC');
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
                    'datebdccli' => $dateCallback

                ],
            ]);
 
        return $this->json($data,200, ["Content-Type" => "application/json"]);
    }

    #[Route('/new', name: 'app_boncommandeclient_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ClientRepository $clientRepository, SluggerInterface $slugger, BoncommandeRepository $boncommandeRepository): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();
        $identreprise = $user->getEntreprise()->getId();
        $boncommande = $boncommandeRepository->findBy(['identreprise' => $identreprise, 'typecommande' => 1, 'deletedAt' => NULL]);
        $client = $clientRepository->findBy(['identreprise' => $identreprise, "deletedAt" => NULL]);


        $boncommandeclient = new Boncommandeclient();
        $form = $this->createForm(BoncommandeclientType::class, $boncommandeclient, ['boncommande' => $boncommande, 'client' => $client]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('brochure')->getData();
            $numdevis = $form->get('numdevis')->getData()->getPo();
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
                $boncommandeclient->setBrochure($newFilename);
            }
            //$entreprise = $user->getEntreprise();
            $boncommandeclient->setIdentreprise($identreprise);
            $boncommandeclient->setNumdevis($numdevis);
            $boncommandeclient->setCreatedBy($this->getUser()->getId());
            $boncommandeclient->setCreatedAt(new DateTime('now'));
            $entityManager->persist($boncommandeclient);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_boncommandeclient_new' : 'app_boncommandeclient_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);


            // $entityManager->persist($boncommandeclient);
            // $entityManager->flush();

            // return $this->redirectToRoute('app_boncommandeclient_index', [], Response::HTTP_SEE_OTHER);
        

        return $this->render('boncommandeclient/new.html.twig', [
            'boncommandeclient' => $boncommandeclient,
            'form' => $form,
            'response' => $response,
        ], $response);
    }

    #[Route('/{id}', name: 'app_boncommandeclient_show', methods: ['GET'])]
    public function show(Boncommandeclient $boncommandeclient): Response
    {
        return $this->render('boncommandeclient/show.html.twig', [
            'boncommandeclient' => $boncommandeclient,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_boncommandeclient_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Boncommandeclient $boncommandeclient, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(BoncommandeclientType::class, $boncommandeclient);
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
                $boncommandeclient->setBrochure($newFilename);
            }
            $boncommandeclient->setUpdatedAt(new \DateTime("now"));
            $boncommandeclient->setUpdatedBy($this->getUser()->getId());
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_boncommandeclient_new' : 'app_boncommandeclient_index';
            if ($nextAction) {
                $this->addFlash('success', 'Modification avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('boncommandeclient/edit.html.twig', [
            'boncommandeclient' => $boncommandeclient,
            'form' => $form,
            'response' => $response,
        ], $response);
    }

    #[Route('/delete', name: 'app_boncommandeclient_delete', methods: ['POST'])]
    public function delete(Request $request, BoncommandeclientRepository $boncommandeclient, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $id = $request->request->get('id-delete');
        $liste = $boncommandeclient->find($id);
        $liste->setDeletedAt(new \DateTime("now"));
        $liste->setDeletedBy($this->getUser()->getId());
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Suppression effectuée avec succès'
        );

        return $this->redirectToRoute('app_boncommandeclient_index', [], Response::HTTP_SEE_OTHER);
    }
}
