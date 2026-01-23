<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\FabricantRepository;
use App\Repository\MarqueRepository;
use App\Repository\TypearticleRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/article')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $entreprise = $this->getUser()->getEntreprise()->getId();
      
        $article = $articleRepository->findBy(['identreprise'=> $entreprise]);
      
        return $this->render('article/index.html.twig', [
            'articles' =>  $article,
        ]);
    }

    #[Route('/lister', name: 'article_lister', methods: ['GET'])]
    public function getListeArticle(Request $request, ArticleRepository $articleRepository): Response
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
 
        $liste = $articleRepository->getListeArticle($critere, $entreprise, 'DESC',$limit,$offset);
        $countliste = $articleRepository->getListeArticle($critere, $entreprise, 'DESC');
       
        $data['recordsTotal']= count($liste);
        $data['recordsFiltered'] = count($countliste);
        //Serialisation
        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer([$normalizer]);
        $data["data"] = $serializer->normalize($liste, null, ['groups' => 'show:liste']);

        return $this->json($data,200, ["Content-Type" => "application/json"]);
    }
    
    #[Route('/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, TypearticleRepository $typearticleRepository, MarqueRepository $marqueRepository, FabricantRepository $fabricantRepository, SluggerInterface $slugger): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        $entreprise = $user->getEntreprise()->getId();
       
        $typearticle = $typearticleRepository->findBy( ['identreprise' =>$entreprise, "deletedAt" => NULL]);
        $marque = $marqueRepository->findBy( ['identreprise' =>$entreprise, "deletedAt" => NULL]);
        $fabricant = $fabricantRepository->findBy( ['identreprise' =>$entreprise, "deletedAt" => NULL]);
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article, ["typearticle" =>$typearticle, 'marque' =>$marque, 'fabricant'=>$fabricant]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entreprise = $this->getUser()->getEntreprise()->getId();
            $brochureFile = $form->get('brochure')->getData();
            $detail = $request->request->all()['detailarticle'];
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $article->setBrochureFilename($newFilename);  
            }
            $article->setDetailarticle($detail);
            //$entreprise = $user->getEntreprise();
            $article->setIdentreprise($entreprise);
            $article->setCreatedBy($this->getUser()->getId());
            $article->setCreatedAt(new DateTime('now'));
            $entityManager->persist($article);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_article_new' : 'app_article_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('article/new.html.twig', [
        'article' => $article,
        'form' => $form,
        'response' => $response,
        ], $response);
    }  

    #[Route('show/{id}', name: 'app_article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    } 

    #[Route('/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager,  TypearticleRepository $typearticleRepository, MarqueRepository $marqueRepository, FabricantRepository $fabricantRepository, SluggerInterface $slugger): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Veuillez vous connecter SVP !!!');
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        $entreprise = $user->getEntreprise()->getId();
       
        $typearticle = $typearticleRepository->findBy( ['identreprise' =>$entreprise, "deletedAt" => NULL]);
        $marque = $marqueRepository->findBy( ['identreprise' =>$entreprise, "deletedAt" => NULL]);
        $fabricant = $fabricantRepository->findBy( ['identreprise' =>$entreprise, "deletedAt" => NULL]);
        $form = $this->createForm(ArticleType::class, $article, ["typearticle" =>$typearticle, 'marque' =>$marque, 'fabricant'=>$fabricant]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('brochure')->getData();
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $article->setBrochureFilename($newFilename);
            }
            $detail = $request->request->all()['detailarticle'];
            if ($detail) {
                $article->setDetailarticle($detail);
            }
            $article->setDetailarticle($detail);
            $article->setUpdatedAt(new \DateTime("now"));
            $article->setUpdatedBy($this->getUser()->getId());
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_article_new' : 'app_article_index';
            if ($nextAction) {
                $this->addFlash('success', 'Modification avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
            'response' => $response,
        ], $response);
    }

    #[Route('/delete', name: 'app_article_delete', methods: ['POST'])]
    public function delete(Request $request, ArticleRepository $articleRepository, EntityManagerInterface $em): Response
    {
        if ($this->denyAccessUnlessGranted("ROLE_USER", "Authentification", "Veuillez vous connecté SVP !!!")) {
            return $this->redirectToRoute("app_login");
        };

        $id = $request->request->get('id-delete');
        $liste = $articleRepository->find($id);
        $liste->setDeletedAt(new \DateTime("now"));
        $liste->setDeletedBy($this->getUser()->getId());
        $em->flush();

        $this->addFlash(
            'success',
            'Suppression effectuée avec succès'
        );
        return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
