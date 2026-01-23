<?php

namespace App\Controller;

use App\Form\BilanoperationType;
use App\Repository\DepenseRepository;
use App\Repository\ObjetdepenseRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BilanController extends AbstractController
{
    #[Route('/bilan', name: 'app_bilan')]
    public function index(): Response
    {
        return $this->render('bilan/index.html.twig', [
            'controller_name' => 'BilanController',
        ]);
    }
    
    
    #[Route('/bilanoperation', name: 'app_bilan_operation', methods: ['GET', 'POST'])]
    public function rechercheDepense(Request $request, DepenseRepository $depenseRepository, ObjetdepenseRepository $objetdepenseRepository,  DateTime $dateFin = null, DateTime $dateDebut = null
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $entreprise = $this->getUser()->getEntreprise();
        $depenses = [];
        $objetdepense = $objetdepenseRepository->findBy(["deletedAt" => NULL, 'identreprise' => $entreprise]);
        //        $difference = $cotisationRepository->getSeanceByDates();
        $searchdepense = $this->createForm(BilanoperationType::class, $depenses, ['objetdepense' => $objetdepense]);
        if ($searchdepense->handleRequest($request)->isSubmitted()) {
            $criteria = $searchdepense->getData();

          
            $criteres = [];

            $criteres["identreprise"] = $entreprise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];
            }
            if ($criteria["objetdepense"]) {
                $criteres["objetdepense"] = $criteria["objetdepense"];
            }
            

            
            $depenses = $depenseRepository->rechercheDepense($criteres, $dateDebut, $dateFin );
            
        }

        return $this->render('bilan/operation.html.twig', [
            'form_recherche' => $searchdepense->createView(),
            'depenses' => $depenses,

        ]);
    }
    
}
