<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\BoncommandeRepository;
use App\Repository\ClientRepository;
use App\Repository\FactureRepository;
use App\Repository\ReglementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(BoncommandeRepository $boncommandeRepository, ArticleRepository $articleRepository , FactureRepository $factureRepository, ClientRepository $clientRepository, ReglementRepository $reglementRepository, ChartBuilderInterface $chartBuilder ): Response
    {
        
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $entreprise = $this->getUser()->getEntreprise()->getId();
        $bonachat = $boncommandeRepository->findBy(['identreprise' => $entreprise, 'typecommande' => 1]);
        $proforma = $boncommandeRepository->findBy(['identreprise' => $entreprise, 'typecommande' => 2]);
        $bon = $boncommandeRepository->findBy(['identreprise' => $entreprise, 'typecommande' => 3 ]);
        $factureimpayee = $factureRepository->findBy(['identreprise' => $entreprise, 'typefacture' => 1]);
        $facturepayee = $factureRepository->findBy(['identreprise' => $entreprise, 'typefacture' => 0]);
        $client = $clientRepository->findBy(['identreprise' => $entreprise]);
        $article = $articleRepository->findByQuantitvente(['identreprise' => $entreprise]);
       
        $labels = [];
        $data = [];
        foreach ($article as $value) {
            $labels[] = $value->getLibellefr();
            $data[] = $value->getQuantitevente();
        }

        

        $chart = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    
                    'data' => $data,
                    'backgroundColor' =>['orange', 'magenta', 'yellow', 'blue','green', 'red','cyan', 'purple',  'pink','brown', ],
                 
                ],
                
         ],
         
       ]);
            
      
        $reglement = $reglementRepository->findBy(['identreprise' => $entreprise]);

 

        // $chart->setOptions([
        //     'scales' => [
        //         'y' => [
        //             'suggestedMin' => 0,
        //             'suggestedMax' => 300,
        //         ],
        //     ],
        // ]);
        

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
            'bonachats' =>$bonachat,
            'proformas' => $proforma,
            'payees' => $facturepayee,
            'articles' => $article,
            'impayees' => $factureimpayee,
            'bon' => $bon ,
            'clients' =>   $client,
            'reglements' =>$reglement,
            'chart' => $chart,

        ]);
    }
}
