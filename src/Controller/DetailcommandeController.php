<?php

namespace App\Controller;

use App\Entity\Detailcommande;
use App\Form\DetailcommandeType;
use App\Repository\DetailcommandeRepository;
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

#[Route('/detailcommande')]
class DetailcommandeController extends AbstractController
{
    #[Route('/', name: 'app_detailcommande_index', methods: ['GET'])]
    public function index(DetailcommandeRepository $detailcommandeRepository): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('detailcommande/index.html.twig', [
            'detailcommandes' => $detailcommandeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_detailcommande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $detailcommande = new Detailcommande();
        $form = $this->createForm(DetailcommandeType::class, $detailcommande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($detailcommande);
            $entityManager->flush();

            return $this->redirectToRoute('app_detailcommande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('detailcommande/new.html.twig', [
            'detailcommande' => $detailcommande,
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


        $data['recordsTotal'] = 0;
        $data['recordsFiltered'] = 0;
        $data['data'] = [];
        $data['draw'] = $draw + 1;
        $critere = [];
        if ($search) {
            $critere['search'] = $search;
        } else {
            $critere = [];
        }

        $liste = $detailreglementRepository->getListeDetailreeglement($critere, $entreprise, 'DESC', $limit, $offset);
        $countliste = $detailreglementRepository->getListeDetailreeglement($critere, $entreprise, 'DESC');

        $data['recordsTotal'] = count($liste);
        $data['recordsFiltered'] = count($countliste);
        //Serialisation date 
        //Serialisation date 
        $dateCallback = function ($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []) {
            return $innerObject instanceof \DateTime ? $innerObject->format('d-m-Y') : '';
        };
        //Serialisation
        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer([$normalizer]);
        $data["data"] = $serializer->normalize(
            $liste,
            null,
            [
                AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,
                AbstractObjectNormalizer::GROUPS => "show:liste",
                AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT => 2,
                AbstractObjectNormalizer::PRESERVE_EMPTY_OBJECTS,
                AbstractObjectNormalizer::SKIP_NULL_VALUES,
                AbstractNormalizer::CALLBACKS => [
                    'datepaiement' => $dateCallback

                ],
            ]
        );

        $data["data"] = $serializer->normalize($liste, null, ['groups' => 'show:liste']);

        return $this->json($data, 200, ["Content-Type" => "application/json"]);
    }


    #[Route('/{id}', name: 'app_detailcommande_show', methods: ['GET'])]
    public function show(Detailcommande $detailcommande): Response
    {
        return $this->render('detailcommande/show.html.twig', [
            'detailcommande' => $detailcommande,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_detailcommande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Detailcommande $detailcommande, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(DetailcommandeType::class, $detailcommande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_detailcommande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('detailcommande/edit.html.twig', [
            'detailcommande' => $detailcommande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_detailcommande_delete', methods: ['POST'])]
    public function delete(Request $request, Detailcommande $detailcommande, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->isCsrfTokenValid('delete'.$detailcommande->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($detailcommande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_detailcommande_index', [], Response::HTTP_SEE_OTHER);
    }
}
