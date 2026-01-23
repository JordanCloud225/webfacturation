<?php

namespace App\Controller;

use App\Entity\Typearticle;
use App\Form\TypearticleType;
use App\Repository\TypearticleRepository;
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
#[Route('/typearticle')]
class TypearticleController extends AbstractController
{
    #[Route('/', name: 'app_typearticle_index', methods: ['GET'])]
    public function index(TypearticleRepository $typearticleRepository): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $entreprise = $this->getUser()->getEntreprise()->getId();
      
        $typearticle = $typearticleRepository->findBy(['identreprise'=> $entreprise]);
      
        return $this->render('typearticle/index.html.twig', [
            'typearticles' =>  $typearticle,
        ]);
    }

    #[Route('/lister', name: 'typearticle_lister', methods: ['GET'])]
    public function getListeTypearticle(Request $request, TypearticleRepository $typearticleRepository): Response
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

        $liste = $typearticleRepository->getListeTypearticle($critere, $entreprise, 'DESC',$limit,$offset);
        $countliste = $typearticleRepository->getListeTypearticle($critere, $entreprise, 'DESC');

        $data['recordsTotal']= count($liste);
        $data['recordsFiltered'] = count($countliste);
        //Serialisation
        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer([$normalizer]);
        $data["data"] = $serializer->normalize($liste, null, ['groups' => 'show:liste']);

        return $this->json($data,200, ["Content-Type" => "application/json"]);
    }
    
    #[Route('/new', name: 'app_typearticle_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $typearticle = new Typearticle();
        $form = $this->createForm(TypearticleType::class, $typearticle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entreprise = $this->getUser()->getEntreprise()->getId();
          
            //$entreprise = $user->getEntreprise();
            $typearticle->setIdentreprise($entreprise);
            $typearticle->setCreatedBy($this->getUser()->getId());
            $typearticle->setCreatedAt(new DateTime('now'));
            $entityManager->persist($typearticle);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_typearticle_new' : 'app_typearticle_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('typearticle/new.html.twig', [
        'typearticle' => $typearticle,
        'form' => $form,
        'response' => $response,
        ], $response);
    }

    #[Route('show/{id}', name: 'app_typearticle_show', methods: ['GET'])]
    public function show(Typearticle $typearticle): Response
    {
        return $this->render('typearticle/show.html.twig', [
            'typearticle' => $typearticle,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_typearticle_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Typearticle $typearticle, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(TypearticleType::class, $typearticle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typearticle->setUpdatedAt(new \DateTime("now"));
            $typearticle->setUpdatedBy($this->getUser()->getId());
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_typearticle_new' : 'app_typearticle_index';
            if ($nextAction) {
                $this->addFlash('success', 'Modification avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('typearticle/edit.html.twig', [
            'typearticle' => $typearticle,
            'form' => $form,
            'response' => $response,
        ], $response);
    }

    #[Route('/delete', name: 'app_typearticle_delete', methods: ['POST'])]
    public function delete(Request $request, TypearticleRepository $typearticleRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $id = $request->request->get('id-delete');
        $liste = $typearticleRepository->find($id);
        $liste->setDeletedAt(new \DateTime("now"));
        $liste->setDeletedBy($this->getUser()->getId());
        $em->flush();

        $this->addFlash(
            'success',
            'Suppression effectuée avec succès'
        );
        return $this->redirectToRoute('app_typearticle_index', [], Response::HTTP_SEE_OTHER);
    }
}
