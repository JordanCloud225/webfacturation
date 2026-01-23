<?php

namespace App\Controller;

use App\Entity\Depense;
use App\Form\DepenseType;
use App\Repository\DepenseRepository;
use App\Repository\ObjetdepenseRepository;
use App\Repository\SoldeRepository;
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
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;



#[Route('/depense')]
class DepenseController extends AbstractController
{
    #[Route('/', name: 'app_depense_index', methods: ['GET'])]
    public function index(DepenseRepository $depenseRepository, SoldeRepository $soldeRepository): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $entreprise = $this->getUser()->getEntreprise()->getId();
      $solde = $soldeRepository->findBy(["entreprise" =>$entreprise]);
        $depense = $depenseRepository->findBy(['identreprise'=> $entreprise]);
      
        return $this->render('depense/index.html.twig', [
            'depenses' =>  $depense,
            'soldes' =>$solde,
        ]);
    }

    #[Route('/lister', name: 'depense_lister', methods: ['GET'])]
    public function getListeDepense(Request $request, DepenseRepository $depenseRepository): Response
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

        $liste = $depenseRepository->getListeDepense($critere, $entreprise, 'DESC',$limit,$offset);
        $countliste = $depenseRepository->getListeDepense($critere, $entreprise, 'DESC');

        $data['recordsTotal']= count($liste);
        $data['recordsFiltered'] = count($countliste);
        //Serialisation date 
        $dateCallback = function ($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []) {
            return $innerObject instanceof \DateTime ? $innerObject->format('d-m-Y') : '';
        };
        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer([$normalizer]);
        $data["data"] = $serializer->normalize($liste, null,
            [
                AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,
                AbstractNormalizer::GROUPS=>"show:liste",
                AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT=>2,
                AbstractObjectNormalizer::PRESERVE_EMPTY_OBJECTS,
                AbstractObjectNormalizer::SKIP_NULL_VALUES,
                AbstractNormalizer::CALLBACKS => [
                    'datedepense' => $dateCallback
        
                ],
            ]);
        // fin serialization date


       // $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
       // $normalizer = new ObjectNormalizer($classMetadataFactory);
       // $serializer = new Serializer([$normalizer]);
      // $data["data"] = $serializer->normalize($liste, null, ['groups' => 'show:liste']);

        return $this->json($data,200, ["Content-Type" => "application/json"]);
    }
    
    #[Route('/new', name: 'app_depense_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ObjetdepenseRepository $objetdepenseRepository ,SoldeRepository $soldeRepository): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $entreprise = $this->getUser()->getEntreprise()->getId();
        $objetdepense = $objetdepenseRepository->findBy( ['identreprise' =>$entreprise, "deletedAt" => NULL]);

        $depense = new Depense();
        $form = $this->createForm(DepenseType::class, $depense, ['objetdepense' =>$objetdepense,]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entreprise = $this->getUser()->getEntreprise()->getId();
            $valeur = $form["montant"]->getData();
          
            //$entreprise = $user->getEntreprise();
            $depense->setIdentreprise($entreprise);
            $depense->setCreatedBy($this->getUser()->getId());

            $dql = $soldeRepository->findBy(['entreprise' => $entreprise]);
            if ($dql) {
                $id = $dql[0]->getId();
                $solde = $soldeRepository->findOneBy(['entreprise'=>$entreprise]);
                $mont = $solde->getMontant();
                $j = 0;
                $j = $mont - $valeur;
                $solde->setMontant($j);
            }

            $depense->setCreatedAt(new DateTime('now'));
            $entityManager->persist($depense);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_depense_new' : 'app_depense_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('depense/new.html.twig', [
        'depense' => $depense,
        'form' => $form,
        'response' => $response,
        ], $response);
    }

    #[Route('show/{id}', name: 'app_depense_show', methods: ['GET'])]
    public function show(Depense $depense): Response
    {
        return $this->render('depense/show.html.twig', [
            'depense' => $depense,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_depense_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Depense $depense,  ObjetdepenseRepository $objetdepenseRepository ,EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $entreprise = $this->getUser()->getEntreprise()->getId();
        $objetdepense = $objetdepenseRepository->findBy( ['identreprise' =>$entreprise, "deletedAt" => NULL]);

        $form = $this->createForm(DepenseType::class, $depense, ['objetdepense' =>$objetdepense,]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $depense->setUpdatedAt(new \DateTime("now"));
            $depense->setUpdatedBy($this->getUser()->getId());
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_depense_new' : 'app_depense_index';
            if ($nextAction) {
                $this->addFlash('success', 'Modification avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('depense/edit.html.twig', [
            'depense' => $depense,
            'form' => $form,
            'response' => $response,
        ], $response);
    }

    #[Route('/delete', name: 'app_depense_delete', methods: ['POST'])]
    public function delete(Request $request, DepenseRepository $depenseRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $id = $request->request->get('id-delete');
        $liste = $depenseRepository->find($id);
        $liste->setDeletedAt(new \DateTime("now"));
        $liste->setDeletedBy($this->getUser()->getId());
        $em->flush();

        $this->addFlash(
            'success',
            'Suppression effectuée avec succès'
        );
        return $this->redirectToRoute('app_depense_index', [], Response::HTTP_SEE_OTHER);
    }
}
