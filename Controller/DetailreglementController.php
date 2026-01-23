<?php

namespace App\Controller;

use App\Entity\Detailreglement;
use App\Form\DetailreglementType;
use App\Repository\DetailreglementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

#[Route('/detailreglement')]
class DetailreglementController extends AbstractController
{
    #[Route('/', name: 'app_detailreglement_index', methods: ['GET'])]
    public function index(DetailreglementRepository $detailreglementRepository): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('detailreglement/index.html.twig', [
            'detailreglements' => $detailreglementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_detailreglement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $detailreglement = new Detailreglement();
        $form = $this->createForm(DetailreglementType::class, $detailreglement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($detailreglement);
            $entityManager->flush();

            return $this->redirectToRoute('app_detailreglement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('detailreglement/new.html.twig', [
            'detailreglement' => $detailreglement,
            'form' => $form,
        ]);
    }

 
    #[Route('/lister', name: 'detailreglement_lister', methods: ['GET'])]
    public function getListeDetailreglement(Request $request, DetailreglementRepository $detailreglementRepository): Response
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

        $liste = $detailreglementRepository->getListeDetailreglement($critere, $entreprise, 'DESC',$limit,$offset);
        $countliste = $detailreglementRepository->getListeDetailreglement($critere, $entreprise, 'DESC');

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
                    'datepaiement' => $dateCallback
        
                ],
            ]);
        // fin serialization date


       // $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
       // $normalizer = new ObjectNormalizer($classMetadataFactory);
       // $serializer = new Serializer([$normalizer]);
      // $data["data"] = $serializer->normalize($liste, null, ['groups' => 'show:liste']);

        return $this->json($data,200, ["Content-Type" => "application/json"]);
    }
    
    #[Route('/{id}', name: 'app_detailreglement_show', methods: ['GET'])]
    public function show(Detailreglement $detailreglement): Response
    {
        return $this->render('detailreglement/show.html.twig', [
            'detailreglement' => $detailreglement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_detailreglement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Detailreglement $detailreglement, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(DetailreglementType::class, $detailreglement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_detailreglement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('detailreglement/edit.html.twig', [
            'detailreglement' => $detailreglement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_detailreglement_delete', methods: ['POST'])]
    public function delete(Request $request, Detailreglement $detailreglement, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->isCsrfTokenValid('delete'.$detailreglement->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($detailreglement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_detailreglement_index', [], Response::HTTP_SEE_OTHER);
    }
}
