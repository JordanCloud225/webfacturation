<?php

namespace App\Controller;

use App\Entity\Typeservice;
use App\Form\TypeserviceType;
use App\Repository\TypeserviceRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
#[Route('/typeservice')]
class TypeserviceController extends AbstractController
{
    #[Route('/', name: 'app_typeservice_index', methods: ['GET'])]
    public function index(TypeserviceRepository $typeserviceRepository): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $entreprise = $this->getUser()->getEntreprise()->getId();
      
        $typeservice = $typeserviceRepository->findBy(['identreprise'=> $entreprise]);
      
        return $this->render('typeservice/index.html.twig', [
            'typeservices' =>  $typeservice,
        ]);
    }

    #[Route('/lister', name: 'typeservice_lister', methods: ['GET'])]
    public function getListeTypeservice(Request $request, TypeserviceRepository $typeserviceRepository): Response
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

        $liste = $typeserviceRepository->getListeTypeservice($critere, $entreprise, 'DESC',$limit,$offset);
        $countliste = $typeserviceRepository->getListeTypeservice($critere, $entreprise, 'DESC');

        $data['recordsTotal']= count($liste);
        $data['recordsFiltered'] = count($countliste);
        //Serialisation
        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer([$normalizer]);
        $data["data"] = $serializer->normalize($liste, null, ['groups' => 'show:liste']);

        return $this->json($data,200, ["Content-Type" => "application/json"]);
    }
    
    #[Route('/new', name: 'app_typeservice_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $typeservice = new Typeservice();
        $form = $this->createForm(TypeserviceType::class, $typeservice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entreprise = $this->getUser()->getEntreprise()->getId();
          
            //$entreprise = $user->getEntreprise();
            $typeservice->setIdentreprise($entreprise);
            $typeservice->setCreatedBy($this->getUser()->getId());
            $typeservice->setCreatedAt(new DateTime('now'));
            $entityManager->persist($typeservice);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_typeservice_new' : 'app_typeservice_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('typeservice/new.html.twig', [
        'typeservice' => $typeservice,
        'form' => $form,
        'response' => $response,
        ], $response);
    }

    #[Route('show/{id}', name: 'app_typeservice_show', methods: ['GET'])]
    public function show(Typeservice $typeservice): Response
    {
        return $this->render('typeservice/show.html.twig', [
            'typeservice' => $typeservice,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_typeservice_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Typeservice $typeservice, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(TypeserviceType::class, $typeservice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typeservice->setUpdatedAt(new \DateTime("now"));
            $typeservice->setUpdatedBy($this->getUser()->getId());
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Modification effectuée avec succès'
            );
            return $this->redirectToRoute('app_typeservice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('typeservice/edit.html.twig', [
            'typeservice' => $typeservice,
            'form' => $form,
        ]);
    }

    #[Route('/delete', name: 'app_typeservice_delete', methods: ['POST'])]
    public function delete(Request $request, TypeserviceRepository $typeserviceRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $id = $request->request->get('id-delete');
        $liste = $typeserviceRepository->find($id);
        $liste->setDeletedAt(new \DateTime("now"));
        $liste->setDeletedBy($this->getUser()->getId());
        $em->flush();

        $this->addFlash(
            'success',
            'Suppression effectuée avec succès'
        );
        return $this->redirectToRoute('app_typeservice_index', [], Response::HTTP_SEE_OTHER);
    }
}
