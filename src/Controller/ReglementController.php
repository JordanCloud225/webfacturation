<?php

namespace App\Controller;

use App\Entity\Detailreglement;
use App\Entity\Reglement;
use App\Form\ReglementType;
use App\Repository\FactureRepository;
use App\Repository\ReglementRepository;
use App\Repository\SoldeRepository;
use App\Service\PdfService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
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

#[Route('/reglement')]
class ReglementController extends AbstractController
{
    #[Route('/', name: 'app_reglement_index', methods: ['GET'])]
    public function index(ReglementRepository $reglementRepository, SoldeRepository $soldeRepository): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $entreprise = $this->getUser()->getEntreprise()->getId();
        $solde = $soldeRepository->findBy(["entreprise" =>$entreprise]);
        $reglement = $reglementRepository->findBy(['identreprise'=> $entreprise]);
      
        return $this->render('reglement/index.html.twig', [
            'reglements' =>  $reglement,
            'soldes' =>$solde,
        ]);
    }

    
 
    #[Route('/lister', name: 'reglement_lister', methods: ['GET'])]
    public function getListeReglement(Request $request, ReglementRepository $reglementRepository): Response
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

        $liste = $reglementRepository->getListeReglement($critere, $entreprise, 'DESC',$limit,$offset);
        $countliste = $reglementRepository->getListeReglement($critere, $entreprise, 'DESC');

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
                    'datereglement' => $dateCallback
        
                ],
            ]);
        // fin serialization date


       // $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
       // $normalizer = new ObjectNormalizer($classMetadataFactory);
       // $serializer = new Serializer([$normalizer]);
      // $data["data"] = $serializer->normalize($liste, null, ['groups' => 'show:liste']);

        return $this->json($data,200, ["Content-Type" => "application/json"]);
    }

    

    #[Route('/new', name: 'app_reglement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $reglement = new Reglement();
        $form = $this->createForm(ReglementType::class, $reglement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reglement);
            $entityManager->flush();

            return $this->redirectToRoute('app_reglement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reglement/new.html.twig', [
            'reglement' => $reglement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reglement_show', methods: ['GET'])]
    public function show(Reglement $reglement): Response
    {
        return $this->render('reglement/show.html.twig', [
            'reglement' => $reglement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reglement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reglement $reglement, ReglementRepository $reglementRepository, FactureRepository $factureRepository, EntityManagerInterface $entityManager, SoldeRepository $soldeRepository): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(ReglementType::class, $reglement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $user = $this->getUser();
            $entreprise = $user->getEntreprise()->getId();
            

            $idfacture = $form['facture']->getData()->getId();

           
            $montantpayee = $form['montantverse']->getData();
            $datepaiement = $form['datereglement']->getData();
                // On crée le solde si solde inexistant ou on incremente le solde
        //    $dql = $reglementRepository->findBy(['facture' => $facture]);

            $dejapaye = $reglement->getMontantpayee();
            
         
            $resteapaye = $form['reste']->getData();
            $mon = $dejapaye +  $montantpayee;
            $reste =   $resteapaye - $montantpayee;

       
        
            $reglement->setMontantpayee($mon);
            $reglement->setReste($reste);

             
                  
                    $facture = $factureRepository->findOneByFacture($idfacture);
                    $solde = $soldeRepository->findOneBy(['entreprise'=>$entreprise]);
                    $montantsolde = $solde->getMontant();
                    $montantantpayefacture = $facture->getMontantpaye();
                    $rest = $facture->getReste();
                    $j = 0;
                    $r = 0;
                    $montSolde = 0;
                    $montSolde = $montantsolde + $montantpayee; 
                    $r = $rest - $montantpayee;
                    $j = $montantantpayefacture + $montantpayee;
                    $solde->setMontant($montSolde);
                    $facture->setMontantpaye($j);
                    $facture->setReste($r);

                    if( $r <= 0){
                        $facture->setTypefacture(0);
                    }
                    else{
                        $facture->setTypefacture(1);
                    }
                          //creation detailreglement
            $detailreglement = new Detailreglement();
            $detailreglement->setReglement($reglement);
            $detailreglement->setMontantpaye($montantpayee);
            $detailreglement->setReste($reste);
            $detailreglement->setIdentreprise($entreprise);
            $detailreglement->setCreatedAt(new DateTime("now"));
            $detailreglement->setDatepaiement($datepaiement);
            $detailreglement->setIdentreprise($entreprise);
            $detailreglement->setCreatedBy($this->getUser()->getId());
            $entityManager->persist($detailreglement);
            $reglement->setUpdatedAt(new DateTime("now"));
            $this->addFlash('success', 'Paiement effectué avec.');
                   // $entityManager->persist($solde);
                    //$entityManager->flush($solde);
                    $entityManager->flush();
                //} else {

            //         $id = $dql[0]->getId();
            //         $activite = $factureRepository->findOneByFacture($id);
            //         $solde = $soldeRepository->findOneBy(['entreprise'=>$entreprise]);
            //         $mont2 = $solde->getMontant();
            //         $mont = $activite->getMontantpaye();
            //         $rest = $activite->getReste();
            //         $j = 0;
            //         $r = 0;
            //         $montSolde = 0;
            //         $montSolde = $mont2 + $montant; 
            //         $r = $rest - $montant;
            //         $j = $mont + $montant;
            //         $solde->setMontant($montSolde);
            //         $activite->setMontantpaye($j);
            //         $activite->setReste($r);

            //               //creation detailreglement
            // $detailreglement = new Detailreglement();
            // $detailreglement->setReglement($reglement);
            // $detailreglement->setMontantpaye($montant);
            // $detailreglement->setReste($reste);
            // $detailreglement->setIdentreprise($entreprise);
            // $detailreglement->setCreatedAt(new DateTime("now"));
            // $detailreglement->setDatepaiement($datepaiement);
            // $detailreglement->setIdentreprise($entreprise);
            // $detailreglement->setCreatedBy($this->getUser()->getId());
            // $entityManager->persist($detailreglement);
            // $reglement->setUpdatedAt(new DateTime("now"));
            // $this->addFlash('success', 'Paiement effectué avec.');
            //        // $entityManager->flush($solde);
            //       //  $entityManager->persist($solde);
                 
               // }
      
              //  $entityManager->flush();
        

            return $this->redirectToRoute('app_reglement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reglement/edit.html.twig', [
            'reglement' => $reglement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/print2', name: 'app_reglement_print2', methods: ['GET', 'POST'])]
    public function printReglement(int $id, ReglementRepository $reglementRepository, PdfService $pdf) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
      
        $entreprise = $this->getUser()->getEntreprise();
        $header = $_GET['header'] ;
        $footer = $_GET['footer'] ;
//        $reglement = $reglementRepository->findAll();
        $reglement = $reglementRepository->findOneById($id);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('reglement/print2.html.twig', [
            'reglements' => $reglement,
            'entreprise' => $entreprise,
            'header' => $header,
            'footer' => $footer,
        ]);
        $pdf->generatePdf($html);   
     
    } 

    #[Route('/delete', name: 'app_reglement_delete', methods: ['POST'])]
    public function delete(Request $request, ReglementRepository $reglementRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $id = $request->request->get('id-delete');
        $liste = $reglementRepository->find($id);
        $liste->setDeletedAt(new \DateTime("now"));
        $liste->setDeletedBy($this->getUser()->getId());
        $em->flush();

        $this->addFlash(
            'success',
            'Suppression effectuée avec succès'
        );
        return $this->redirectToRoute('app_reglement_index', [], Response::HTTP_SEE_OTHER);
    }
}
