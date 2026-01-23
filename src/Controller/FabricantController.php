<?php

namespace App\Controller;

use App\Entity\Fabricant;
use App\Form\FabricantType;
use App\Repository\FabricantRepository;
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
#[Route('/fabricant')]
class FabricantController extends AbstractController
{
    #[Route('/', name: 'app_fabricant_index', methods: ['GET'])]
    public function index(FabricantRepository $fabricantRepository): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $entreprise = $this->getUser()->getEntreprise()->getId();
      
        $fabricant = $fabricantRepository->findBy(['identreprise'=> $entreprise]);
      
        return $this->render('fabricant/index.html.twig', [
            'fabricants' =>  $fabricant,
        ]);
    }

    #[Route('/lister', name: 'fabricant_lister', methods: ['GET'])]
    public function getListeFabricant(Request $request, FabricantRepository $fabricantRepository): Response
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

        $liste = $fabricantRepository->getListeFabricant($critere, $entreprise, 'DESC',$limit,$offset);
        $countliste = $fabricantRepository->getListeFabricant($critere, $entreprise, 'DESC');

        $data['recordsTotal']= count($liste);
        $data['recordsFiltered'] = count($countliste);
        //Serialisation
        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer([$normalizer]);
        $data["data"] = $serializer->normalize($liste, null, ['groups' => 'show:liste']);

        return $this->json($data,200, ["Content-Type" => "application/json"]);
    }
    
    #[Route('/new', name: 'app_fabricant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $fabricant = new Fabricant();
        $form = $this->createForm(FabricantType::class, $fabricant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entreprise = $this->getUser()->getEntreprise()->getId();
          
            //$entreprise = $user->getEntreprise();
            $fabricant->setIdentreprise($entreprise);
            $fabricant->setCreatedBy($this->getUser()->getId());
            $fabricant->setCreatedAt(new DateTime('now'));
            $entityManager->persist($fabricant);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_fabricant_new' : 'app_fabricant_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('fabricant/new.html.twig', [
        'fabricant' => $fabricant,
        'form' => $form,
        'response' => $response,
        ], $response);
    }

    #[Route('show/{id}', name: 'app_fabricant_show', methods: ['GET'])]
    public function show(Fabricant $fabricant): Response
    {
        return $this->render('fabricant/show.html.twig', [
            'fabricant' => $fabricant,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_fabricant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Fabricant $fabricant, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(FabricantType::class, $fabricant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fabricant->setUpdatedAt(new \DateTime("now"));
            $fabricant->setUpdatedBy($this->getUser()->getId());
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_fabricant_new' : 'app_fabricant_index';
            if ($nextAction) {
                $this->addFlash('success', 'Modification avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('fabricant/edit.html.twig', [
            'fabricant' => $fabricant,
            'form' => $form,
            'response' => $response,
        ], $response);
    }

    #[Route('/delete', name: 'app_fabricant_delete', methods: ['POST'])]
    public function delete(Request $request, FabricantRepository $fabricantRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $id = $request->request->get('id-delete');
        $liste = $fabricantRepository->find($id);
        $liste->setDeletedAt(new \DateTime("now"));
        $liste->setDeletedBy($this->getUser()->getId());
        $em->flush();

        $this->addFlash(
            'success',
            'Suppression effectuée avec succès'
        );
        return $this->redirectToRoute('app_fabricant_index', [], Response::HTTP_SEE_OTHER);
    }
}
