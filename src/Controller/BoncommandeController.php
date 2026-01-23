<?php



namespace App\Controller;



use App\Entity\Boncommande;



use App\Entity\Facture;

use App\Entity\Reglement;

use App\Form\BoncommandeType;



use App\Form\DevisType;

use App\Form\ProformaType;

use App\Repository\ArticleRepository;

use App\Repository\BoncommandeRepository;

use App\Repository\ClientRepository;

use App\Repository\DetailcommandeRepository;

use App\Repository\DetailreglementRepository;

use App\Repository\FactureRepository;

use App\Repository\ServiceRepository;

use App\Service\NumberToWordsService;

use App\Service\PdfService;

use DateTime;

use Doctrine\ORM\EntityManagerInterface;

use App\Service\MpdfService;

use Dompdf\Dompdf;

use Dompdf\Options;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use Symfony\Component\Serializer\Serializer;

use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;

use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;

use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;



#[Route('/boncommande')]

class BoncommandeController extends AbstractController

{

     

    private $PDFService;



    public function __construct(PdfService $PDFService)

    {

        $this->PDFService = $PDFService;

    }



    #[Route('/', name: 'app_boncommande_index', methods: ['GET'])]

    public function index(BoncommandeRepository $boncommandeRepository): Response

    {

        if (!$this->isGranted('ROLE_USER')) {

            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');

            return $this->redirectToRoute('app_login');

        }

        $entreprise = $this->getUser()->getEntreprise()->getId();



        $boncommande = $boncommandeRepository->findBy(['identreprise' => $entreprise]);



        return $this->render('boncommande/index.html.twig', [

            'boncommandes' =>  $boncommande,

        ]);

    }



    #[Route('/proforma', name: 'app_boncommande_proforma', methods: ['GET'])]

    public function proformaIndex(BoncommandeRepository $boncommandeRepository): Response

    {

        if (!$this->isGranted('ROLE_USER')) {

            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');

            return $this->redirectToRoute('app_login');

        }



        $entreprise = $this->getUser()->getEntreprise()->getId();



        $boncommande = $boncommandeRepository->findBy(['identreprise' => $entreprise, 'typecommande' => 2]);



        return $this->render('boncommande/proforma.html.twig', [

            'boncommandes' =>  $boncommande,

        ]);

    }



    #[Route('/index2', name: 'app_boncommande_index2', methods: ['GET'])]

    public function index2(BoncommandeRepository $boncommandeRepository): Response

    {

        if (!$this->isGranted('ROLE_USER')) {

            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');

            return $this->redirectToRoute('app_login');

        }



        $entreprise = $this->getUser()->getEntreprise()->getId();



        $boncommande = $boncommandeRepository->findBy(['identreprise' => $entreprise, 'typecommande' => 1]);



        return $this->render('boncommande/devis.html.twig', [

            'boncommandes' =>  $boncommande,

        ]);

    }



    #[Route('/detailcommande', name: 'app_boncommande_detailcommande', methods: ['GET'])]

    public function indexDetailcommande(DetailcommandeRepository $detailcommandeRepository): Response

    {

        if (!$this->isGranted('ROLE_USER')) {

            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');

            return $this->redirectToRoute('app_login');

        }

        $entreprise = $this->getUser()->getEntreprise()->getId();



        $detailcommande = $detailcommandeRepository->findBy(['identreprise' => $entreprise]);



        return $this->render('boncommande/detailcommande.html.twig', [

            'detailcommandes' =>  $detailcommande,

        ]);

    }



    #[Route('/facture', name: 'app_boncommande_facture', methods: ['GET'])]

    public function indexFacture(FactureRepository $factureRepository): Response

    {

        if (!$this->isGranted('ROLE_USER')) {

            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');

            return $this->redirectToRoute('app_login');

        }

        $entreprise = $this->getUser()->getEntreprise()->getId();



        $facture = $factureRepository->findBy(['identreprise' => $entreprise]);



        return $this->render('boncommande/facture.html.twig', [

            'factures' =>  $facture,

        ]);

    }



    #[Route('/facture/print/{id}', name: 'app_boncommande_facture_print', methods: ['GET'])]

    public function printFacture(Facture $facture, DetailcommandeRepository $detailcommandeRepository, BoncommandeRepository $boncommandeRepository, MpdfService $mpdfService, NumberToWordsService $numberToWordsService,ClientRepository $clientRepository): Response {

        

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');



        $logo = $mpdfService->getLogo();

        $boncommande = $facture->getBoncommande();

        $detailcommandes = $detailcommandeRepository->findBy(['boncommande' => $boncommande]);

        $total = $boncommande->getTotal();

        $montantLettre = $numberToWordsService->convertToFrench($total);

        $client = $boncommande->getClient();



        $html = $this->renderView('boncommande/facture_print.html.twig', [

            'detailcommandes' => $detailcommandes,

            'facture' => $facture,

            'logo' => $logo,

            'total' => $total,

            'montantLettre' => $montantLettre,

            'client' => $client,

            'boncommande' => $boncommande,

        ]);



        $header = $mpdfService->getHtmlHeader($logo);

        $footer = $mpdfService->getHtmlFooter();



        $pdf = $mpdfService->generatePdf($html, [

            'header_html' => $header,

            'footer_html' => $footer

        ]);



        return new Response($pdf, 200, [

            'Content-Type' => 'application/pdf',

            'Content-Disposition' => 'inline; filename="facture_'.$facture->getId().'.pdf"',

        ]);

    }



    #[Route('/lister', name: 'boncommande_lister', methods: ['GET'])]

    public function getListeBoncommande(Request $request, BoncommandeRepository $boncommandeRepository): Response

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



        $liste = $boncommandeRepository->getListeBoncommande($critere, $entreprise, 'DESC', $limit, $offset);

        $countliste = $boncommandeRepository->getListeBoncommande($critere, $entreprise, 'DESC');



        $data['recordsTotal'] = count($liste);

        $data['recordsFiltered'] = count($countliste);

        //Serialisation

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

                AbstractNormalizer::GROUPS => "show:liste",

                AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT => 2,

                AbstractObjectNormalizer::PRESERVE_EMPTY_OBJECTS,

                AbstractObjectNormalizer::SKIP_NULL_VALUES,

                AbstractNormalizer::CALLBACKS => [

                    'datecommande' => $dateCallback



                ],

            ]

        );



        return $this->json($data, 200, ["Content-Type" => "application/json"]);

    }





    #[Route('/listerproforma', name: 'boncommande_proforma', methods: ['GET'])]

    public function getListeProforma(Request $request, BoncommandeRepository $boncommandeRepository): Response

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



        $liste = $boncommandeRepository->getListeProforma($critere, $entreprise, 'DESC', $limit, $offset);

        $countliste = $boncommandeRepository->getListeProforma($critere, $entreprise, 'DESC');



        $data['recordsTotal'] = count($liste);

        $data['recordsFiltered'] = count($countliste);

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

                AbstractNormalizer::GROUPS => "show:liste",

                AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT => 2,

                AbstractObjectNormalizer::PRESERVE_EMPTY_OBJECTS,

                AbstractObjectNormalizer::SKIP_NULL_VALUES,

                AbstractNormalizer::CALLBACKS => [

                    'dateproforma' => $dateCallback



                ],

            ]

        );



        return $this->json($data, 200, ["Content-Type" => "application/json"]);

    }



    #[Route('/devis', name: 'boncommande_devis', methods: ['GET'])]

    public function getListeDevis(Request $request, BoncommandeRepository $boncommandeRepository): Response

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



        $liste = $boncommandeRepository->getListeDevis($critere, $entreprise, 'DESC', $limit, $offset);

        $countliste = $boncommandeRepository->getListeDevis($critere, $entreprise, 'DESC');



        $data['recordsTotal'] = count($liste);

        $data['recordsFiltered'] = count($countliste);

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

                AbstractNormalizer::GROUPS => "show:liste",

                AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT => 2,

                AbstractObjectNormalizer::PRESERVE_EMPTY_OBJECTS,

                AbstractObjectNormalizer::SKIP_NULL_VALUES,

                AbstractNormalizer::CALLBACKS => [

                    'datedevis' => $dateCallback



                ],

            ]

        );



        return $this->json($data, 200, ["Content-Type" => "application/json"]);

    }



    #[Route('/listerdetailcommande', name: 'detailbon_lister', methods: ['GET'])]

    public function getListeDetailcommande(Request $request, DetailcommandeRepository $detailcommandeRepository): Response

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



        $liste = $detailcommandeRepository->getListeDetailcommande($critere, $entreprise, 'DESC', $limit, $offset);

        $countliste = $detailcommandeRepository->getListeDetailcommande($critere, $entreprise, 'DESC');



        $data['recordsTotal'] = count($liste);

        $data['recordsFiltered'] = count($countliste);

        //Serialisation

        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());

        $normalizer = new ObjectNormalizer($classMetadataFactory);

        $serializer = new Serializer([$normalizer]);

        $data["data"] = $serializer->normalize($liste, null, ['groups' => 'show:liste']);



        return $this->json($data, 200, ["Content-Type" => "application/json"]);

    }





    #[Route('/listerfacture', name: 'facture_lister', methods: ['GET'])]

    public function getListeFacture(Request $request, FactureRepository $factureRepository): Response

    {





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



        $liste = $factureRepository->getListeFacture($critere, $entreprise, 'DESC', $limit, $offset);

        $countliste = $factureRepository->getListeFacture($critere, $entreprise, 'DESC');



        $data['recordsTotal'] = count($liste);

        $data['recordsFiltered'] = count($countliste);

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

                AbstractNormalizer::GROUPS => "show:liste",

                AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT => 2,

                AbstractObjectNormalizer::PRESERVE_EMPTY_OBJECTS,

                AbstractObjectNormalizer::SKIP_NULL_VALUES,

                AbstractNormalizer::CALLBACKS => [

                    'datefacture' => $dateCallback



                ],

            ]

        );

        // $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());

        // $normalizer = new ObjectNormalizer($classMetadataFactory);

        // $serializer = new Serializer([$normalizer]);

        // $data["data"] = $serializer->normalize($liste, null, ['groups' => 'show:liste']);



        return $this->json($data, 200, ["Content-Type" => "application/json"]);

    }





    #[Route('/new', name: 'app_boncommande_new', methods: ['GET', 'POST'])]

    public function new(Request $request, EntityManagerInterface $entityManager, BoncommandeRepository $boncommandeRepository, ClientRepository $clientRepository): Response

    {

        if (!$this->isGranted('ROLE_USER')) {

            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');

            return $this->redirectToRoute('app_login');

        }

        $user = $this->getUser();

        $entreprise = $user->getEntreprise()->getId();

        $client = $clientRepository->findBy(['identreprise' => $entreprise, "deletedAt" => NULL]);



        //création d'un nouveau numero de demande d'achat

        $dataBC = $boncommandeRepository->findlastsavetoday($entreprise);

        $exPo = $dataBC ? $dataBC->getPo() : null;

        if (!$exPo) {

            $newPo = "1" . "-" . date('dmy') . "-" . "SOCAF";

        } else {

            $number =  explode('-', $exPo)[0] + 1;

            $newPo = $number. "-" . date('dmy') . "-" ."SOCAF";

        }



        $boncommande = new Boncommande();

        $form = $this->createForm(DevisType::class, $boncommande, ['client' => $client,]);

        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {


            $type = $type = $form['type']->getData();

            $total = 0;

            foreach ($form->getData()->getDetailcommandes()->toArray() as $commande) {

                $a = 0;

                if ($commande->getArticle()) {

                    $prix = strval($commande->getArticle()->getPrixunitaire());

                    $commande->setPrixunitaire($prix);

                } elseif ($commande->getService()) {

                    $prix = strval($commande->getService()->getPrixunitaire());

                    $commande->setPrixunitaire($prix);

                }

                $quantite = $commande->getQuantite();

                $a = $prix *  $quantite;

                $total += $a;

                $commande->setSoustotal($a);

                $commande->setCreatedBy($this->getUser()->getId());

                $commande->setIdentreprise($entreprise);

                $commande->setCreatedAt(new DateTime('now'));

                

                $entityManager->persist($commande);

            }

            if ($type == 0) {

                

                // Profoma sans prise en compte de la TVA



                $montht = $total;

                // $monttc =  $total -  $montantremise;

                //  $reste = $monttc -  $montantpaye;

                // $boncommande->setTtc($monttc);

                // $boncommande->setReste($monttc);

                $boncommande->setMontantht($montht);

                $boncommande->setTva(0);

            } else {

                // Proforma avec prise en compte de la TVA

                // Fin Bon dommande

                $monttva = $total  * 0.18;

                $montht = $total;

                $monttc =  $monttva + $total ;

                //  $reste = $monttc -  $montantpaye;

                $boncommande->setTtc($monttc);

                $boncommande->setReste($monttc);

                $boncommande->setMontantht($montht);

                $boncommande->setTva($monttva);

            }

            

            // Edition du devis= demande achat avec Type egal 1

            $user = $this->getUser();

            $entreprise = $user->getEntreprise()->getId();

            $boncommande->setIdentreprise($entreprise);

            $boncommande->setPo($newPo);

            $boncommande->setCodedevis($newPo);

            $boncommande->setCodecommande($newPo);

            $boncommande->setCreatedBy($this->getUser()->getId());

            $boncommande->setCreatedAt(new DateTime('now'));

            $boncommande->setTypecommande(1);

            //Code du devis

            // Combinaison de la date du jiour avec les 4 derniers chiffres de itentifiant de la facture

            $devis = $boncommandeRepository->findBy(array(), array('id' => 'desc'), 1, 0);

            $id = 0;

            foreach ($devis as $value) {

                $id = $value->getId();

            }

            $val = $id + 1;

            $idCommande = substr($val, 0, 4);



            $datedujour = $form["datedevis"]->getData();

            $formatDate = $datedujour->format('dm');

            $formatAn = $datedujour->format('Y');



            $number = substr($formatAn, -2);;







            $code = $idCommande . '-' . $formatDate . $number;



            $boncommande->setCodedevis($code);

            // fin code devis

            $entityManager->persist($boncommande);

            //  $tauxremise = $form['tauxremise']->getData();





            //   $quantite = $form['quantite']->getData();

            //$tva = $form['tva']->getData();

            $datedevis = $form['datedevis']->getData();

            //$montantremise = $form['montantremise']->getData();



            $total = 0;



            // Create an ArrayCollection of the current Tag objects in the database

            foreach ($form->getData()->getDetailcommandes()->toArray() as $commande) {

                 if ($commande->getArticle()) {

                    $a = 0;

                    $articlenewqte= 0;

                    $prix = $commande->getArticle()->getPrixunitaire();

                    $article = $commande->getArticle();

                    $qtevente = $article->getQuantitevente();



                

                    $quantite = $commande->getQuantite();

                    $articlenewqte = $qtevente + $quantite;

                    $article->setQuantitevente($articlenewqte);



                    $commande->setPrixunitaire($prix);

                    $a = $prix *  $quantite;

                    $total += $a;



                    $commande->setSoustotal($a);

                    $commande->setCreatedBy($this->getUser()->getId());

                    $commande->setIdentreprise($entreprise);

                    $commande->setCreatedAt($datedevis);





                    $entityManager->persist($commande);

                } else {

                    $a = 0;

                    $prix = $commande->getService()->getPrixunitaire();

                

                    $quantite = $commande->getQuantite();



                    $commande->setPrixunitaire($prix);

                    $a = $prix *  $quantite;

                    $total += $a;



                    $commande->setSoustotal($a);

                    $commande->setCreatedBy($this->getUser()->getId());

                    $commande->setIdentreprise($entreprise);

                    $commande->setCreatedAt($datedevis);





                    $entityManager->persist($commande);

                }

                 }

               

            // Fin Bon dommande

            // $tauxtva = $tva / 100;

            // $monttva = $total * $tauxtva;

            $montht = $total;

            // $monttc = ($total * $monttva) + $total;

            // $reste = $monttc -  $montantpaye;

            // $boncommande->setTtc($monttc);

            $boncommande->setReste($montht);

            $boncommande->setMontantht($montht);



            $entityManager->flush();

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_boncommande_new' : 'app_boncommande_index2';

            if ($nextAction) {

                $this->addFlash('success', 'Création demande d\'achat avec succès.');

            }

            return $this->redirectToRoute($nextAction);

        }

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);



        return $this->render('boncommande/new.html.twig', [

            'boncommande' => $boncommande,

            'form' => $form->createView(),

            'response' => $response,

            'newPo' => $newPo

        ], $response);

    }





    #[Route('/{id}/proform', name: 'app_boncommande_proform', methods: ['GET', 'POST'])]

    public function proformaEdit(Request $request, Boncommande $boncommande, EntityManagerInterface $entityManager, BoncommandeRepository $boncommandeRepository, DetailcommandeRepository $detailcommandeRepository, ClientRepository $clientRepository): Response

    {

        if (!$this->isGranted('ROLE_USER')) {

            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');

            return $this->redirectToRoute('app_login');

        }



        $user = $this->getUser();

        $entreprise = $user->getEntreprise()->getId();

        $client = $clientRepository->findBy(['identreprise' => $entreprise, "deletedAt" => NULL]);

        $idboncommande = $boncommande->getId();

        $detailcommande = $detailcommandeRepository->findBy(['boncommande' => $idboncommande]);

        $form = $this->createForm(ProformaType::class, $boncommande, ['client' => $client]);

        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {

            // Transformation en facture profomat en mettant typecommande  à 2

            $user = $this->getUser();

            $entreprise = $user->getEntreprise()->getId();



            //$boncommande->setIdentreprise($entreprise);

            $boncommande->setUpdatedBy($this->getUser()->getId());

            $boncommande->setUpdatedAt(new DateTime('now'));

            $boncommande->setTypecommande(2);

            $datebon = $form['dateproforma']->getData();

            //Code du devis

            // Combinaison de la date du jiour avec les 4 derniers chiffres de itentifiant de la facture

            $devis = $boncommandeRepository->findBy(array(), array('id' => 'desc'), 1, 0);

            $id = 0;

            foreach ($devis as $value) {

                $id = $value->getId();

            }

            $val = $id + 1;

            $idCommande = substr($val, 0, 6);



            $datedujour = $datebon;

      

            //

            $formatDate = $datedujour->format('dm');

            $formatAn = $datedujour->format('Y');



            $number = substr($formatAn, -2);

            

            $code = $idCommande . '-' . $formatDate . $number;

            //

           

            $boncommande->setCodeproforma($code);

            // fin code devis

            $boncommande->setTypecommande(2);

            $entityManager->persist($boncommande);



            //Creation detailcommande



            $montantremise = $form['montantremise']->getData();

            //   $boncommande->setDateproforma($datebon);



            $total = 0;



            // Create an ArrayCollection of the current Tag objects in the database

            foreach ($form->getData()->getDetailcommandes()->toArray() as $commande) {

                $a = 0;

                $prix = $commande->getPrixunitaire();

                $quantite = $commande->getQuantite();

                $a = $prix *  $quantite;

                $total += $a;

                $commande->setSoustotal($a);

                $commande->setCreatedBy($this->getUser()->getId());

                $commande->setIdentreprise($entreprise);

                $commande->setCreatedAt(new DateTime('now'));



                $entityManager->persist($commande);

            }



            // if ($type == 0) {



            //     // Profoma sans prise en compte de la TVA



            //     $montht = $total;

            //     $monttc =  $total -  $montantremise;

            //     //  $reste = $monttc -  $montantpaye;

            //     $boncommande->setTtc($monttc);

            //     $boncommande->setReste($monttc);

            //     $boncommande->setMontantht($montht);

            //     $boncommande->setTva(0);

            // } else {

            //     // Proforma avec prise en compte de la TVA

            //     // Fin Bon dommande



            //     $monttva = ($total - $montantremise) * 0.18;

            //     $montht = $total;

            //     $monttc =  $monttva + ($total - $montantremise);

            //     //  $reste = $monttc -  $montantpaye;

            //     $boncommande->setTtc($monttc);

            //     $boncommande->setReste($monttc);

            //     $boncommande->setMontantht($montht);

            //     $boncommande->setTva($monttva);

            // }



            $boncommande->setUpdatedAt(new \DateTime("now"));

            $boncommande->setUpdatedBy($this->getUser()->getId());

            $entityManager->flush();



            return $this->redirectToRoute('app_boncommande_proforma', [], Response::HTTP_SEE_OTHER);

        }

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);



        return $this->render('boncommande/proform.html.twig', [

            'boncommande' => $boncommande,

            'detailcommande' => $detailcommande,

            'form' => $form,

            'response' => $response,

        ], $response);

    }





    #[Route('/{id}/edit', name: 'app_boncommande_edit', methods: ['GET', 'POST'])]

    public function edit(Request $request, Boncommande $boncommande, FactureRepository $factureRepository, EntityManagerInterface $entityManager, BoncommandeRepository $boncommandeRepository, ClientRepository $clientRepository, DetailcommandeRepository $detailcommandeRepository): Response

    {

        if (!$this->isGranted('ROLE_USER')) {

            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');

            return $this->redirectToRoute('app_login');

        }

        $user = $this->getUser();

        $entreprise = $user->getEntreprise()->getId();

        $client = $clientRepository->findBy(['identreprise' => $entreprise, "deletedAt" => NULL]);

        $form = $this->createForm(BoncommandeType::class, $boncommande, ['client' => $client,]);

        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {            

            // Validation de la commande pour devenir facture definitive avec type à 3



            $user = $this->getUser();

            //  $entreprise = $user->getEntreprise()->getId();

            //$boncommande->setIdentreprise($entreprise);

            $boncommande->setUpdatedBy($this->getUser()->getId());

            $boncommande->setUpdatedAt(new DateTime('now'));

            $boncommande->setTypecommande(3);

            $type = $form['type']->getData();

            //Code du devis

            // Combinaison de la date du jiour avec les 4 derniers chiffres de itentifiant de la facture

            $devis = $boncommandeRepository->findBy(array(), array('id' => 'desc'), 1, 0);

            $id = 0;

            foreach ($devis as $value) {

                $id = $value->getId();

            }

            $val = $id + 1;

            $idCommande = substr($val, 0, 6);





            $datecommande = $form["datecommande"]->getData();

            //

            $formatDate = $datecommande->format('dm');

            $formatAn = $datecommande->format('Y');



            $number = substr($formatAn, -2);;







            $code = $idCommande . '-' . $formatDate.$number;

          

            //

       





            $boncommande->setCodecommande($code);

            // On recupere la valeur de TVA enregistree au prealable

            //$tva = $boncommande->getTva();

            // fin code devis

            $entityManager->persist($boncommande);



            //Creation detailcommande







            $montantremise = $form['montantremise']->getData();

            $po = $form['po']->getData();

            $jobtitle = $form['jobtitle']->getData();

            $sitelocation = $form['sitelocation']->getData();





            $total = 0;



            //  $boncommande->setDevis(1);



            $idbdc = $boncommande->getId();

            $detailcommande = $detailcommandeRepository->findBy(['boncommande' => $idbdc]);

            foreach ($detailcommande as $key => $ligneDC) {

                $qte = $ligneDC->getQuantite();

                $article = $ligneDC->getArticle();

                if ($article) {

                    $qtestock = $article->getQuantitestock();

                    $newstock = $qtestock - $qte;

                    $article->setQuantitestock($newstock);

                    $entityManager->persist($article);

                } else {



                }



            }

            //decrementation de l'article en stock egalement sur la facture definitive





            // Create an ArrayCollection of the current Tag objects in the database

            foreach ($form->getData()->getDetailcommandes()->toArray() as $commande) {

                $a = 0;

                $prix = $commande->getPrixunitaire();

                $quantite = $commande->getQuantite();

                $a = $prix *  $quantite;

                $total += $a;

                $commande->setSoustotal($a);

                $commande->setCreatedBy($this->getUser()->getId());

                //  $commande->setIdentreprise($entreprise);

                //  $commande->setCreatedAt(new DateTime('now'));



                $entityManager->persist($commande);

            }



            if ($type == 0) {







                $total2 = 0;

                // Create an ArrayCollection of the current Tag objects in the database

                foreach ($form->getData()->getDetailcommandes()->toArray() as $commande) {

                    $a = 0;

                    $prix = $commande->getPrixunitaire();

                    $quantite = $commande->getQuantite();

                    $a = $prix *  $quantite;

                    $total2 += $a;

                    $commande->setSoustotal($a);

                    $commande->setCreatedBy($this->getUser()->getId());

                    //  $commande->setIdentreprise($entreprise);

                    //  $commande->setCreatedAt(new DateTime('now'));





                    $entityManager->persist($commande);

                }





                // Fin Bon dommande

                //  $id = $boncommande->getId();

                // $dql = $boncommandeRepository->findOneById($id);

                //   $tva = $dql->getTva();



                // $monttva = ($total - $montantremise) * 0.18;

                // $boncommande->setTva(0); / tva defini au formulaire devis converti en proforma

                $montht1 = ($total2 -  $montantremise);

                $monttc =   ($total2 -  $montantremise);



                $boncommande->setTtc($monttc);

                $boncommande->setReste($monttc);

                $boncommande->setMontantht($montht1);

                $entityManager->persist($commande);



                // Creation de facture

                $facture =  new Facture();

                if ($po) {

                    $facture->setPo($po);



                }

                if ($jobtitle) {

                    $facture->setJobtitle($jobtitle);



                }

                if ($sitelocation) {

                    $facture->setSitelocation($sitelocation);



                }

            //    $facture->setTva(0);

                $facture->setMontantpaye(0);

                $mont1 = $montht1;



                $facture->setBoncommande($boncommande);

                $facture->setIdentreprise($entreprise);

                $facture->setDatefacture($datecommande);

                $facture->setCreatedAt(new DateTime('now'));

                $facture->setMontantfacture($montht1);

                $facture->setMontantht($montht1);

                $facture->setNetpayee($mont1);

                $facture->setReste($mont1);

                $facture->setMontantremise($montantremise);

                // $facture->setNumfacture(012345);

                $facture->setTypefacture(1);

                $facture->setCreatedBy($this->getUser()->getId());







                // Numero commande

                // Combinaison de la date du jiour avec les 4 derniers chiffres de itentifiant de la facture

                $factures = $factureRepository->findBy(array(), array('id' => 'desc'), 1, 0);

                $id = 0;

                foreach ($factures as $value) {

                    $id = $value->getId();

                }

                $val = $id + 1;

                $idCommande = substr($val, 0, 6);



                $datedujour = $datecommande;

                $formatDate = $datecommande->format('dm');

                $formatAn = $datecommande->format('Y');

    

                $number = substr($formatAn, -2);;

    

    

    

                $code = $idCommande . '-' . $formatDate.$number;

                $facture->setNumfacture($code);



                // Fin cone facture



                $entityManager->persist($facture);



                // Creation detail reglement

                $reglement = new Reglement();

                $reglement->setFacture($facture);

                $reglement->setMontantpayee(0);

                $reglement->setReste($monttc);

                $reglement->setDatereglement(new DateTime('now'));

                $reglement->setCreatedAt(new DateTime('now'));

                $reglement->setCreatedBy($this->getUser()->getId());

                $reglement->setIdentreprise($entreprise);



                $entityManager->persist($reglement);

            } else {







                $total3 = 0;

                // Create an ArrayCollection of the current Tag objects in the database

                foreach ($form->getData()->getDetailcommandes()->toArray() as $commande) {

                    $a = 0;

                    $prix = $commande->getPrixunitaire();

                    $quantite = $commande->getQuantite();

                    $a = $prix *  $quantite;

                    $total3 += $a;

                    $commande->setSoustotal($a);

                    $commande->setCreatedBy($this->getUser()->getId());

                    //  $commande->setIdentreprise($entreprise);

                    //  $commande->setCreatedAt(new DateTime('now'));





                    $entityManager->persist($commande);

                }





                // Fin Bon dommande

                $tauxtva = 0.18;



                $monttva = ($total3 - $montantremise) * $tauxtva;

                $montht = $total3;

                $monttc = $monttva + ($total3 - $montantremise);

                $boncommande->setTtc($monttc);

                $boncommande->setReste($monttc);

                $boncommande->setMontantht($montht);





                // Creation de facture

                $facture =  new Facture();

                $facture->setBoncommande($boncommande);

                $facture->setIdentreprise($entreprise);

                $facture->setDatefacture($datecommande);

                $facture->setCreatedAt(new DateTime('now'));

                $facture->setMontantfacture($montht);

                $facture->setMontantht($montht);

                $facture->setNetpayee($monttc);

                $facture->setReste($monttc);

                $facture->setMontantremise($montantremise);

                // $facture->setNumfacture(012345);

                $facture->setTypefacture(1);

                $facture->setCreatedBy($this->getUser()->getId());



                $facture->setTva($monttva);



                // Numero commande

                // Combinaison de la date du jiour avec les 4 derniers chiffres de itentifiant de la facture

                $factures = $factureRepository->findBy(array(), array('id' => 'desc'), 1, 0);

                $id = 0;

                foreach ($factures as $value) {

                    $id = $value->getId();

                }

                $val = $id + 1;

                $idCommande = substr($val, 0, 4);



                $datedujour = ($datecommande);

                $formatDate = $datedujour->format('dmY');





                $code = $idCommande . '-' . $formatDate;

                $facture->setNumfacture($code);



                // Fin cone facture



                $entityManager->persist($facture);



                // Creation detail reglement

                $reglement = new Reglement();

                $reglement->setFacture($facture);

                $reglement->setMontantpayee(0);

                $reglement->setReste($monttc);

                $reglement->setDatereglement($datecommande);

                $reglement->setCreatedAt(new DateTime('now'));

                $reglement->setCreatedBy($this->getUser()->getId());

                $reglement->setIdentreprise($entreprise);



                $entityManager->persist($reglement);

            }





            $boncommande->setUpdatedAt(new \DateTime("now"));

            $boncommande->setUpdatedBy($this->getUser()->getId());

            $this->addFlash(

                'success',

                'Facture definitive créée avec succès, vous pouvez proceder au paiement maintenant'

            );

            $entityManager->flush();

            return $this->redirectToRoute('app_reglement_index', [], Response::HTTP_SEE_OTHER);

        }



        $response = new Response(null, $form->isSubmitted() ? 422 : 200);



        return $this->render('boncommande/edit.html.twig', [

            'boncommande' => $boncommande,

            'form' => $form,



        ]);

    }



    #[Route('/valide', name: 'app_boncommande_valide', methods: ['POST'])]

    public function valideBon(Request $request, BoncommandeRepository $boncommandeRepository, EntityManagerInterface $em): Response

    {



        $id = $request->request->get('id-valide');

        $liste = $boncommandeRepository->find($id);

        $liste->setDeletedAt(new \DateTime("now"));

        $liste->setDeletedBy($this->getUser()->getId());

        $em->flush();



        $this->addFlash(

            'success',

            'Validation effectuée avec succès'

        );

        return $this->redirectToRoute('app_boncommande_index', [], Response::HTTP_SEE_OTHER);

    }



    #[Route('/delete', name: 'app_boncommande_delete', methods: ['POST'])]

    public function delete(Request $request, BoncommandeRepository $boncommandeRepository, EntityManagerInterface $em): Response

    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');



        $id = $request->request->get('id-delete');

        $liste = $boncommandeRepository->find($id);

        $liste->setDeletedAt(new \DateTime("now"));

        $liste->setDeletedBy($this->getUser()->getId());

        $em->flush();



        $this->addFlash(

            'success',

            'Suppression effectuée avec succès'

        );

        return $this->redirectToRoute('app_boncommande_index', [], Response::HTTP_SEE_OTHER);

    }



    #[Route('/{id}/printbon', name: 'boncommande_printbon', methods: ['GET', 'POST'])]

    public function printBon(int $id, BoncommandeRepository $boncommandeRepository, PdfService $pdf, MpdfService $mpdfService)

    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $entreprise = $this->getUser()->getEntreprise();

        $boncommande = $boncommandeRepository->findOneById($id);

            $header = $_GET['header'] ;

            $footer = $_GET['footer'] ;

        // Vérifier si le fichier existe



        $html = $this->renderView('boncommande/printbon.html.twig', [

            'boncommandes' => $boncommande,

            'entreprise' => $entreprise,

            'header' => $header,

            'footer' => $footer,

        ]);

            $pdf->generatePdf($html);



    }



    #[Route('/{id}/printrecu', name: 'boncommande_printrecu', methods: ['GET', 'POST'])]

    public function printRecu(int $id, DetailreglementRepository $detailreglementRepository, PdfService $pdf)

    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');



        $entreprise = $this->getUser()->getEntreprise();



        //$reglement = $reglementRepository->findAll();

        $boncommande = $detailreglementRepository->findOneById($id);



        // Retrieve the HTML generated in our twig file

        $html = $this->renderView('boncommande/printrecu.html.twig', [

            'detailreglement' => $boncommande,

            'entreprise' => $entreprise,

        ]);

        $pdf->generatePdf($html);

    }



    #[Route('/{id}/print2', name: 'boncommande_printdevis', methods: ['GET', 'POST'])]

    public function printDevis(int $id, BoncommandeRepository $boncommandeRepository, PdfService $pdf)

    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');



        $entreprise = $this->getUser()->getEntreprise();

        $header = $_GET['header'] ;

        $footer = $_GET['footer'] ;

        //$reglement = $reglementRepository->findAll();

        $boncommande = $boncommandeRepository->findOneById($id);

        // Retrieve the HTML generated in our twig file

        $html = $this->renderView('boncommande/printdevis.html.twig', [

            'boncommandes' => $boncommande,

            'entreprise' => $entreprise,

            'header' => $header,

            'footer' => $footer,

        ]);

        $pdf->generatePdf($html);

    }



    #[Route('/{id}/printdevisclient', name: 'boncommande_printdevis_client', methods: ['GET', 'POST'])]

    public function printDevisclient(int $id, BoncommandeRepository $boncommandeRepository, PdfService $pdf)

    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');



        $entreprise = $this->getUser()->getEntreprise();

        $header = $_GET['header'] ;

        $footer = $_GET['footer'] ;

        //$reglement = $reglementRepository->findAll();

        $boncommande = $boncommandeRepository->findOneById($id);

        $client = $boncommande->getClient()->getLibelle();

        // Retrieve the HTML generated in our twig file

        $html = $this->renderView('boncommande/printdevisclient.html.twig', [

            'boncommandes' => $boncommande,

            'entreprise' => $entreprise,

            'header' => $header,

            'footer' => $footer,

            'client' => $client,

        ]);

        $pdf->generatePdf($html);

    }



    #[Route('/{id}/printproforma', name: 'boncommande_printproforma', methods: ['GET', 'POST'])]

    public function printProforma(int $id, BoncommandeRepository $boncommandeRepository, PdfService $pdf)

    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');



        $entreprise = $this->getUser()->getEntreprise();

        $header = $_GET['header'] ;

        $footer = $_GET['footer'] ;

        //        $reglement = $reglementRepository->findAll();

        $boncommande = $boncommandeRepository->findOneById($id);

        // dd($boncommande->getDetailcommandes()->toArray());

        // Retrieve the HTML generated in our twig file

        $html = $this->renderView('boncommande/printproforma.html.twig', [

            'boncommandes' => $boncommande,

            'entreprise' => $entreprise,

            'header' => $header,

            'footer' => $footer,

        ]);

        $pdf->generatePdf($html);

    }



    #[Route('/{id}/editDemandeDachat', name: 'app_demandedachat_edit', methods: ['GET', 'POST'])]

    public function editdemandedachat(Request $request, Boncommande $boncommande, DetailcommandeRepository $detailcommandeRepository, ClientRepository $clientRepository, BoncommandeRepository $boncommandeRepository, EntityManagerInterface $entityManager): Response

    {

        if (!$this->isGranted('ROLE_USER')) {

            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');

            return $this->redirectToRoute('app_login');

        }



        $user = $this->getUser();

        $entreprise = $user->getEntreprise()->getId();

        $client = $clientRepository->findBy(['identreprise' => $entreprise, "deletedAt" => NULL]);

        $idboncommande = $boncommande->getId();

        $detailcommande = $detailcommandeRepository->findBy(['boncommande' => $idboncommande]);

        $tva = $boncommande->getTva();

        $form = $this->createForm(DevisType::class, $boncommande, ['client' => $client, 'tva' => $tva]);

        $form->handleRequest($request);

        $po = $boncommande->getPo();

        if ($form->isSubmitted() && $form->isValid()) {

            

            $type = $type = $form['type']->getData();

            $total = 0;

             foreach ($form->getData()->getDetailcommandes()->toArray() as $commande) {

                if ($commande->getArticle()) {

                    $prix = strval($commande->getArticle()->getPrixunitaire());

                    $commande->setPrixunitaire($prix);

                } elseif ($commande->getService()) {

                    $prix = strval($commande->getService()->getPrixunitaire());

                    $commande->setPrixunitaire($prix);

                }

                $prix = $commande->getPrixunitaire();

                $quantite = $commande->getQuantite();

                $prixtotal = $prix *  $quantite;

                $total += $prixtotal;

                $commande->setSoustotal($prixtotal);

                $commande->setUpdatedBy($this->getUser()->getId());

                $commande->setIdentreprise($entreprise);

                $commande->setUpdatedAt(new DateTime('now'));



                $entityManager->persist($commande);

            }



            if ($type == 0) {

                $tva = $boncommande->getTva();

                if ( $tva == 0 ) {

                    //...

                } else {

                    // Profoma sans prise en compte de la TVA

                $montht = $boncommande->getTva() / 0.18 ;

                // $monttc =  $total -  $montantremise;

                //  $reste = $monttc -  $montantpaye;

                // $boncommande->setTtc($monttc);

                // $boncommande->setReste($monttc);

                $boncommande->setMontantht($montht);

                $boncommande->setTva(0);

                $boncommande->setTtc($montht);

                }

                

            } else {

                $tva = $boncommande->getTva();

                if ( $tva == 0 ) {

                    // Proforma avec prise en compte de la TVA

                    // Fin Bon dommande



                    $monttva = $total  * 0.18;

                    $montht = $total;

                    $monttc =  $monttva + $total ;

                    //  $reste = $monttc -  $montantpaye;

                    $boncommande->setTtc($monttc);

                    $boncommande->setReste($monttc);

                    $boncommande->setMontantht($montht);

                    $boncommande->setTva($monttva);                

                } else {

                    //...

                }

            }

            dd($boncommande);



            // Transormation en facture profomat en mettant typecommande  à 2

            $entityManager->flush();



            return $this->redirectToRoute('app_boncommande_index2', [], Response::HTTP_SEE_OTHER);

        }



        return $this->render('boncommande/editdemandedachat.html.twig', [

            'boncommande' => $boncommande,

            'form' => $form,

            'newPo' => $po,

        ]);

    }



    #[Route('/{id}/editproforma', name: 'app_proforma_edit', methods: ['GET', 'POST'])]

    public function editproforma(Request $request, Boncommande $boncommande,DetailcommandeRepository $detailcommandeRepository, ClientRepository $clientRepository, EntityManagerInterface $entityManager): Response

    { 

        if (!$this->isGranted('ROLE_USER')) {

            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');

            return $this->redirectToRoute('app_login');

        }



        $user = $this->getUser();

        $entreprise = $user->getEntreprise()->getId();

        $client = $clientRepository->findBy(['identreprise' => $entreprise, "deletedAt" => NULL]);

        $idboncommande = $boncommande->getId();

        $detailcommande = $detailcommandeRepository->findBy(['boncommande' => $idboncommande]);

        $form = $this->createForm(ProformaType::class, $boncommande, ['client' => $client]);

        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();



            return $this->redirectToRoute('app_boncommande_proforma', [], Response::HTTP_SEE_OTHER);

        }



        return $this->render('boncommande/editproforma.html.twig', [

            'boncommande' => $boncommande,

            'form' => $form,

            'detailcommande' => $detailcommande,

        ]);

    }



    #[Route('/verify_DA', name: 'verify_DA', methods: ['POST'])]

    public function demandedachatverif(Request $request, ArticleRepository $articleRepository, ServiceRepository $serviceRepository): Response

    {



        // try {

            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        //     $result = [];

        //     $total = 0;

        //     $data = json_decode($request->getContent(), true);

        //     if (isset($data['services'])) {

        //      $donnees = $data['services'] ;

        //         if (empty($donnees)) {

        //         throw new \Exception('Aucun service reçu');

        //         }

        //         foreach ($donnees as $key => $value) {

        //             $service = $serviceRepository->find($value['service']);

        //             $quantite = $value['quantite'];  

        //             $prixunitaireservice = $service->getPrixunitaire();

        //             $totalprixservice = $prixunitaireservice * $quantite ;

        //             $total += $totalprixservice; 

        //             $result[] = [

        //                 'service' => $service->getLibellefr(),

        //                 'quantiteservice' => (int)$quantite,

        //                 'prixunitaireservice' => $prixunitaireservice,

        //                 'totalprixservice' => $totalprixservice,

        //                 'total' => $total,

        //             ];

        //         }

        //     } elseif (isset($data['articles'])) {

        //      $donnees = $data['articles'] ;

        //         if (empty($donnees)) {

        //         throw new \Exception('Aucun article reçu');

        //         }

        //         foreach ($donnees as $key => $value) {

        //             $article = $articleRepository->find($value['article']);

        //             $quantite = $value['quantite'];  

        //             $prixunitairearticle = $article->getPrixunitaire();

        //             $totalprixarticle = $prixunitairearticle * $quantite;

        //             $total += $totalprixarticle; 

        //             $result[] = [

        //                 'article' => $article->getLibellefr(),

        //                 'quantitearticle' => (int)$quantite,

        //                 'prixunitairearticle' => $prixunitairearticle,

        //                 'totalprixarticle' => $totalprixarticle,                        

        //                 'total' => $total,

        //             ];

        //         }

        //     }

        //     return new JsonResponse([

        //     'status' => 'success',

        //     'data' => $result,

        //     'total' => $total

        //     ]);

            

        //     } catch (\Exception $e) {

        //         return new JsonResponse([

        //             'status' => 'error',

        //             'message' => $e->getMessage()

        //         ], 400);

        // }





    $data = json_decode($request->getContent(), true);

    $articles = $data['articles'] ?? [];

    $services = $data['services'] ?? [];

    

    $result = [];

    $totalGeneral = 0;

    

    // Traitement des articles

    foreach ($articles as $item) {

        $article = $articleRepository->find($item['article']);

        if ($article) {

            $quantite = $item['quantite'] ?? 1;

            $prixUnitaire = $article->getPrixunitaire();

            $total = $prixUnitaire * $quantite;

            $totalGeneral += $total;

            

            $result[] = [

                'type' => 'article',

                'designation' => $article->getLibellefr(),

                'article' => $article->getLibellefr(),

                'prixunitaire' => $prixUnitaire,

                'quantite' => $quantite,

                'totalprix' => $total,

            ];

        }

    }

    

    // Traitement des services

    foreach ($services as $item) {

        $service = $serviceRepository->find($item['service']);

        if ($service) {

            $quantite = $item['quantite'] ?? 1;

            $prixUnitaire = $service->getPrixunitaire();

            $total = $prixUnitaire * $quantite;

            $totalGeneral += $total;

            

            $result[] = [

                'type' => 'service',

                'designation' => $service->getLibellefr(),

                'service' => $service->getLibellefr(),

                'prixunitaire' => $prixUnitaire,

                'quantite' => $quantite,

                'totalprix' => $total,

            ];

        }

    }

    

    return $this->json([

        'status' => 'success',

        'data' => $result,

        'total' => $totalGeneral,

    ]);

            

    }



       #[Route('/delDA/{id}', name: 'app_del_DeamandeDachat', methods: ['DELETE'])]

public function supprimerArticle(int $id, EntityManagerInterface $em, DetailcommandeRepository $detailcommanderepository ): JsonResponse

{

    $lignecommande = $detailcommanderepository->find($id);

    

    if (!$lignecommande) {

        return $this->json(['success' => false, 'message' => 'Article non trouvé']);

    }else {

        $lignecommande->setDeletedAt(new \DateTime('now'));

        $lignecommande->setDeletedBy($this->getUser()->getId());

     

    }

        $em->flush();

    

    return $this->json(['success' => true]);

}



       #[Route('/delDS/{id}', name: 'app_del_DeamandeDachat', methods: ['DELETE'])]

public function supprimerService(int $id, EntityManagerInterface $em, DetailcommandeRepository $detailcommanderepository ): JsonResponse

{

    $lignecommande = $detailcommanderepository->find($id);

    

    if (!$lignecommande) {

        return $this->json(['success' => false, 'message' => 'Article non trouvé']);

    }else {

        $lignecommande->setDeletedAt(new \DateTime('now'));

        $lignecommande->setDeletedBy($this->getUser()->getId());

    }

        $em->flush();

    

    return $this->json(['success' => true]);

}



}



