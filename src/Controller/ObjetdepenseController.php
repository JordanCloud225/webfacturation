<?php

namespace App\Controller;

use App\Entity\Objetdepense;
use App\Form\ObjetdepenseType;
use App\Repository\ObjetdepenseRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
#[Route('/objetdepense')]
class ObjetdepenseController extends AbstractController
{
    #[Route('/', name: 'app_objetdepense_index', methods: ['GET'])]
    public function index(ObjetdepenseRepository $objetdepenseRepository): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $entreprise = $this->getUser()->getEntreprise()->getId();
      
        $objetdepense = $objetdepenseRepository->findBy(['identreprise'=> $entreprise]);
      
        return $this->render('objetdepense/index.html.twig', [
            'objetdepenses' =>  $objetdepense,
        ]);
    }

    #[Route('/lister', name: 'objetdepense_lister', methods: ['GET'])]
    public function getListeObjetdepense(Request $request, ObjetdepenseRepository $objetdepenseRepository): Response
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

        $liste = $objetdepenseRepository->getListeObjetdepense($critere, $entreprise, 'DESC',$limit,$offset);
        $countliste = $objetdepenseRepository->getListeObjetdepense($critere, $entreprise, 'DESC');

        $data['recordsTotal']= count($liste);
        $data['recordsFiltered'] = count($countliste);
        //Serialisation
        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer([$normalizer]);
        $data["data"] = $serializer->normalize($liste, null, ['groups' => 'show:liste']);

        return $this->json($data,200, ["Content-Type" => "application/json"]);
    }
    
    #[Route('/new', name: 'app_objetdepense_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $objetdepense = new Objetdepense();
        $form = $this->createForm(ObjetdepenseType::class, $objetdepense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entreprise = $this->getUser()->getEntreprise()->getId();
          
            //$entreprise = $user->getEntreprise();
            $objetdepense->setIdentreprise($entreprise);
            $objetdepense->setCreatedBy($this->getUser()->getId());
            $objetdepense->setCreatedAt(new DateTime('now'));
            $entityManager->persist($objetdepense);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_objetdepense_new' : 'app_objetdepense_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('objetdepense/new.html.twig', [
        'objetdepense' => $objetdepense,
        'form' => $form,
        'response' => $response,
        ], $response);
    }

    #[Route('show/{id}', name: 'app_objetdepense_show', methods: ['GET'])]
    public function show(Objetdepense $objetdepense): Response
    {
        return $this->render('objetdepense/show.html.twig', [
            'objetdepense' => $objetdepense,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_objetdepense_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Objetdepense $objetdepense, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(ObjetdepenseType::class, $objetdepense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $objetdepense->setUpdatedAt(new \DateTime("now"));
            $objetdepense->setUpdatedBy($this->getUser()->getId());
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_objetdepense_new' : 'app_objetdepense_index';
            if ($nextAction) {
                $this->addFlash('success', 'Modification avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('objetdepense/edit.html.twig', [
            'objetdepense' => $objetdepense,
            'form' => $form,
            'response' => $response,
        ], $response);
    }

    #[Route('/delete', name: 'app_objetdepense_delete', methods: ['POST'])]
    public function delete(Request $request, ObjetdepenseRepository $objetdepenseRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $id = $request->request->get('id-delete');
        $liste = $objetdepenseRepository->find($id);
        $liste->setDeletedAt(new \DateTime("now"));
        $liste->setDeletedBy($this->getUser()->getId());
        $em->flush();

        $this->addFlash(
            'success',
            'Suppression effectuée avec succès'
        );
        return $this->redirectToRoute('app_objetdepense_index', [], Response::HTTP_SEE_OTHER);
    }
}
