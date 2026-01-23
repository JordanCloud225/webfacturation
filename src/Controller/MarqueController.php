<?php

namespace App\Controller;

use App\Entity\Marque;
use App\Form\MarqueType;
use App\Repository\MarqueRepository;
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
#[Route('/marque')]
class MarqueController extends AbstractController
{
    #[Route('/', name: 'app_marque_index', methods: ['GET'])]
    public function index(MarqueRepository $marqueRepository): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $entreprise = $this->getUser()->getEntreprise()->getId();
      
        $marque = $marqueRepository->findBy(['identreprise'=> $entreprise]);
      
        return $this->render('marque/index.html.twig', [
            'marques' =>  $marque,
        ]);
    }

    // #[Route('/lister', name: 'marque_lister', methods: ['GET'])]
    // public function getListeMarque(Request $request, MarqueRepository $marqueRepository, SerializerInterface $serializer): Response
    // {
    //     $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    //     $limit   = $request->query->get("length");
    //     $offset  = $request->query->get("start");
    //     $draw    = $request->query->get("draw");
    //     $searchValue    = $request->query->all("search");
    //     $search = $searchValue["value"];
    //     $entreprise = 0;
    //     $entreprise = $this->getUser()->getEntreprise()->getId();
    //     // Récupération des variables GET
   

    //     $data['recordsTotal']=0;
    //     $data['recordsFiltered']=0;
    //     $data['data']=[];
    //     $data['draw']=$draw + 1;
    //   $critere = [];
    //     if($search){
    //         $critere['search'] = $search;

    //     }else{
    //         $critere = [];
    //     }

    //     $liste = $marqueRepository->getListeMarque($critere, $entreprise, 'DESC',$limit,$offset);
    //     $countliste = $marqueRepository->getListeMarque($critere, $entreprise, 'DESC');

    //     $data['recordsTotal']= count($liste);
    //     $data['recordsFiltered'] = count($countliste);
    //     //Serialisation
    //     $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
    //     $normalizer = new ObjectNormalizer($classMetadataFactory);
    //     $serializer = new Serializer([$normalizer]);
    //     $data["data"] = $serializer->normalize($liste, null, ['groups' => 'show:liste']);

    //     return $this->json($data,200, ["Content-Type" => "application/json"]);
    // }
    
    #[Route('/lister', name: 'marque_lister', methods: ['GET'])]
public function getListeMarque(Request $request, MarqueRepository $marqueRepository): Response
{
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    $limit = $request->query->get("length");
    $offset = $request->query->get("start");
    $draw = (int) $request->query->get("draw");
    $searchValue = $request->query->all("search");
    $search = $searchValue["value"] ?? null;
    
    $entreprise = $this->getUser()->getEntreprise()->getId();

    $critere = $search ? ['search' => $search] : [];

    $liste = $marqueRepository->getListeMarque($critere, $entreprise, 'DESC', $limit, $offset);
    


    $countliste = $marqueRepository->getListeMarque($critere, $entreprise, 'DESC');
    
    // Dans votre contrôleur
$classMetadataFactory = new \Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory(
    new \Symfony\Component\Serializer\Mapping\Loader\AttributeLoader()
);

$metadata = $classMetadataFactory->getMetadataFor(\App\Entity\Marque::class);
$attributsTrouves = [];

foreach ($metadata->getAttributesMetadata() as $attr) {
    $attributsTrouves[$attr->getName()] = $attr->getGroups();
}

return $this->json([
    'diagnostic' => $attributsTrouves
]);
    
//     $dataArray = [];

// foreach ($liste as $marque) {
//     $dataArray[] = [
//         'id' => $marque->getId(),
//         'libellefr' => $marque->getLibellefr(), // On force l'appel du getter
//         'createdAt' => $marque->getCreatedAt() ? $marque->getCreatedAt()->format('Y-m-d') : null,
//     ];
// }

// return $this->json([
//     'draw' => (int)$draw + 1,
//     'recordsTotal' => count($countliste),
//     'recordsFiltered' => count($countliste),
//     'data' => $dataArray
// ]);

   // ARCHITECTURE : On retourne directement les objets. 
    // Symfony va appeler son Serializer interne automatiquement.
    return $this->json([
        'draw' => (int)$draw + 1,
        'recordsTotal' => count($countliste),
        'recordsFiltered' => count($countliste),
        'data' => $liste // <--- On passe les objets Marque ici
    ], 200, [], [
        'groups' => 'show:liste', // <--- Doit être identique dans Marque.php
        'circular_reference_handler' => function ($object) {
            return $object->getId();
        }
    ]);
}
    
    #[Route('/new', name: 'app_marque_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $marque = new Marque();
        $form = $this->createForm(MarqueType::class, $marque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entreprise = $this->getUser()->getEntreprise()->getId();
          
            //$entreprise = $user->getEntreprise();
            $marque->setIdentrepprise($entreprise);
            $marque->setCreatedBy($this->getUser()->getId());
            $marque->setCreatedAt(new DateTime('now'));
            $entityManager->persist($marque);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_marque_new' : 'app_marque_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('marque/new.html.twig', [
        'marque' => $marque,
        'form' => $form,
        'response' => $response,
        ], $response);
    }

    #[Route('show/{id}', name: 'app_marque_show', methods: ['GET'])]
    public function show(Marque $marque): Response
    {
        return $this->render('marque/show.html.twig', [
            'marque' => $marque,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_marque_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Marque $marque, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(MarqueType::class, $marque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $marque->setUpdatedAt(new \DateTime("now"));
            $marque->setUpdatedBy($this->getUser()->getId());
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_marque_new' : 'app_marque_index';
            if ($nextAction) {
                $this->addFlash('success', 'Modification avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('marque/edit.html.twig', [
            'marque' => $marque,
            'form' => $form,
            'response' => $response,
        ], $response);
    }

    #[Route('/delete', name: 'app_marque_delete', methods: ['POST'])]
    public function delete(Request $request, MarqueRepository $marqueRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $id = $request->request->get('id-delete');
        $liste = $marqueRepository->find($id);
        $liste->setDeletedAt(new \DateTime("now"));
        $liste->setDeletedBy($this->getUser()->getId());
        $em->flush();

        $this->addFlash(
            'success',
            'Suppression effectuée avec succès'
        );
        return $this->redirectToRoute('app_marque_index', [], Response::HTTP_SEE_OTHER);
    }
}
