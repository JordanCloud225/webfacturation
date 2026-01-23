<?php

namespace App\Controller;

use App\Entity\Conditionoffre;
use App\Form\ConditionoffreType;
use App\Repository\ConditionoffreRepository;
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

#[Route('/conditionoffre')]
final class ConditionoffreController extends AbstractController
{
    #[Route(name: 'app_conditionoffre_index', methods: ['GET'])]
    public function index(ConditionoffreRepository $conditionoffreRepository): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $conditionoffre = $conditionoffreRepository->findBy(['deletedAt' => NULL]);
        return $this->render('conditionoffre/index.html.twig', [
            'conditionoffres' => $conditionoffre,
        ]);
    }

    #[Route('/new', name: 'app_conditionoffre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $conditionoffre = new Conditionoffre();
        $form = $this->createForm(ConditionoffreType::class, $conditionoffre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $conditionoffre->setCreatedBy($this->getUser()->getId());
            $conditionoffre->setCreatedAt(new DateTime('now'));
            $entityManager->persist($conditionoffre);
            $entityManager->flush();
             $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_conditionoffre_new' : 'app_conditionoffre_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            };

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('conditionoffre/new.html.twig', [
            'conditionoffre' => $conditionoffre,
            'form' => $form,
        ]);
    }

         #[Route('/lister', name: 'condition_lister', methods: ['GET'])]
    public function getListeCondition(Request $request, ConditionoffreRepository $conditionoffreRepository): Response
    {

        $limit   = $request->query->get("length");
        $offset  = $request->query->get("start");
        $draw    = $request->query->get("draw");
        $searchValue    = $request->query->all("search");
        $search = $searchValue["value"];
        // $entreprise = 0;
        // $entreprise = $this->getUser()->getEntreprise()->getId();
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

        $liste = $conditionoffreRepository->getListeCondition($critere, 'DESC',$limit,$offset);
        $countliste = $conditionoffreRepository->getListeCondition($critere, 'DESC');
        $data['recordsTotal']= count($liste);
        $data['recordsFiltered'] = count($countliste);
        //Serialisation
        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer([$normalizer]);
        $data["data"] = $serializer->normalize($liste, null, ['groups' => 'show:liste']);

        return $this->json($data,200, ["Content-Type" => "application/json"]);
    }

    #[Route('/{id}', name: 'app_conditionoffre_show', methods: ['GET'])]
    public function show(Conditionoffre $conditionoffre): Response
    {
        return $this->render('conditionoffre/show.html.twig', [
            'conditionoffre' => $conditionoffre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_conditionoffre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Conditionoffre $conditionoffre, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(ConditionoffreType::class, $conditionoffre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $conditionoffre->setUpdatedAt(new \DateTime("now"));
            $conditionoffre->setUpdatedBy($this->getUser()->getId());
            $entityManager->flush();
             $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_conditionoffre_new' : 'app_conditionoffre_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            };

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('conditionoffre/edit.html.twig', [
            'conditionoffre' => $conditionoffre,
            'form' => $form,
        ]);
    }

    #[Route('/delete', name: 'app_conditionoffre_delete', methods: ['POST'])]
    public function delete(Request $request, ConditionoffreRepository $conditionoffreRepository, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $id = $request->request->get('id-delete');
        $liste = $conditionoffreRepository->find($id);
        $liste->setDeletedAt(new \DateTime("now"));
        $liste->setDeletedBy($this->getUser()->getId());
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Suppression effectuée avec succès'
        );
        return $this->redirectToRoute('app_conditionoffre_index', [], Response::HTTP_SEE_OTHER);
    }


}
