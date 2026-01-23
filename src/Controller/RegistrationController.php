<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Entity\Solde;
use App\Entity\User;
use App\Form\EditProfileType;
use App\Form\EntrepriseType;
use App\Form\RegistrationFormType;
use App\Form\UpdateuserType;
use App\Form\UserType;
use App\Repository\EntrepriseRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class RegistrationController extends AbstractController
{

    use ClientIp;

    #[Route('/user', name: 'app_register_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('registration/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/entreprise', name: 'app_entreprise_index', methods: ['GET'])]
    public function indexEntreprise(EntrepriseRepository $entrepriseRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $entreprise = $entrepriseRepository->findAll();
        return $this->render('registration/entreprise.html.twig', [
            'entreprises' => $entreprise,
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Ouverture de compte revient a creer le compte entreprise et correspondre a user cree en meme temps

            $libelle = $form['libelle']->getData();
            $siren = $form['siren']->getData();
            $competance= $form['competance']->getData();
            $codenaf = $form['codenaf']->getData();
            $numtva = $form['numtva']->getData();
            $adresse = $form['adresse']->getData();
            $complementadresse = $form['complementadresse']->getData();
            $ville = $form['ville']->getData();
            $pays = $form['pays']->getData();
            $contact2 = $form['contact2']->getData();
            $contact1 = $form['contact1']->getData();
            $email1 = $form['email1']->getData();
            $codepostal = $form['codepostal']->getData();
            $langue = $form['langue']->getData();
            $siteweb = $form['siteweb']->getData();
            $sigle = $form['sigle']->getData();
       

            $entreprise = new Entreprise();
            $entreprise->setLibelle($libelle);
            $entreprise->setSiren($siren);
            $entreprise->setCodenaf($codenaf);
            $entreprise->setNumtva($numtva);
            $entreprise->setCompetance($competance);
            $entreprise->setAdresse($adresse);
            $entreprise->setComplementadresse($complementadresse);
            $entreprise->setVille($ville);
            $entreprise->setPays($pays);
            $entreprise->setContact2($contact2);
            $entreprise->setContact1($contact1);
            $entreprise->setEmail($email1);
            $entreprise->setCodepostal($codepostal);
            $entreprise->setLangue($langue);
            $entreprise->setSiteweb($sigle);
            $entreprise->setSigle($siteweb);
            $entreprise->setEtat(0);
            $entreprise->setCreatedAt(new DateTime("now"));
           // $entreprise->setCreatedFromIp($this->GetIp());

            // Logo entreprise
            $brochureFile2 = $form->get('brochure2')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile2) {
                $originalFilename2 = pathinfo($brochureFile2->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename2 = $slugger->slug($originalFilename2);
                $newFilename2 = $safeFilename2 . '-' . uniqid() . '.' . $brochureFile2->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile2->move(
                        $this->getParameter('images_directory'),
                        $newFilename2
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                } 

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $entreprise->setBrochureFilename($newFilename2);
            }
            $entityManager->persist($entreprise);

         
            $solde = new Solde();
            $solde->setEntreprise($entreprise);
            $solde->setMontant(0);


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
                $user->setBrochureFilename($newFilename);
            }
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setEntreprise($entreprise);
            $entityManager->persist($user);
            $entityManager->persist($solde);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('registration_success');
        }

        return $this->render('registration/register.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/listeuser', name: 'app_registration_user', methods: ['GET'])]
    public function listeUser(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        $entreprise = $this->getUser()->getEntreprise();
        $user = $userRepository->findBy(['entreprise' => $entreprise]);
        return $this->render('registration/user.html.twig', [
            'users' => $user,
        ]);
    }

    #[Route('/newuser', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, SluggerInterface $slugger): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entreprise = $this->getUser()->getEntreprise();

            /** @var UploadedFile $brochureFile */
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
                $user->setBrochureFilename($newFilename);
            }
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setEntreprise($entreprise);
            $entityManager->persist($user);
            $this->addFlash('message', 'Création du compte avec succès.');

            $entityManager->flush();

            return $this->redirectToRoute('app_registration_user', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('registration/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/success', name: 'registration_success', methods: ['GET', 'POST'])]
    public function success(): Response
    {

        return $this->render('registration/success.html.twig', []);
    }

    #[Route('entreprise/{id}/entreprise', name: 'app_entreprise_delete', methods: ['POST'])]
    public function delete(Request $request, Entreprise $entreprise, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $entreprise->getId(), $request->request->get('_token'))) {


          //  $entreprise->setDeletedFromIp($this->GetIp());
            $entreprise->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $entreprise->setEtat(0);
            $entreprise->setDeletedBy($user);
            $this->addFlash('message', 'Fermeture du compte avec succès.');

            $entityManager->flush();
        }

        return $this->redirectToRoute('app_entreprise_index');
    }

    #[Route('/{id}/entreprise/', name: 'app_entreprise_active', methods: ['POST'])]
    public function activeEntreprise(Request $request, Entreprise $entreprise, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('active' . $entreprise->getId(), $request->request->get('_token'))) {

            $user = $this->getUser();
            $entreprise->setUpdatedBy($user);
           // $entreprise->setUpdatedFromIp($this->GetIp());
            $entreprise->setEtat(1);

            //$user = $this->getUser();
            $this->addFlash('message', 'Activation du compte avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_entreprise_index');
    }

    #[Route('/{id}/uservalidate/', name: 'app_registration_active', methods: ['POST'])]
    public function validateUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('active' . $user->getId(), $request->request->get('_token'))) {


            $user->setEtat(1);

            // $user->setDeletedAt(NULL);

            $this->addFlash('message', 'Activation du compte avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_registration_user');
    }

    #[Route('/{id}/deleteuser', name: 'app_registration_delete', methods: ['POST'])]
    public function deleteUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {


            $user->setEtat(0);

            //$user->setDeletedAt(new DateTime("now"));

            $this->addFlash('message', 'Fermeture du compte avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_registration_user');
    }

    #[Route('/{id}/edit', name: 'app_entreprise_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Entreprise $entreprise, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        $form = $this->createForm(EntrepriseType::class, $entreprise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $brochureFile2 = $form->get('brochure2')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile2) {
                $originalFilename2 = pathinfo($brochureFile2->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename2 = $slugger->slug($originalFilename2);
                $newFilename2 = $safeFilename2 . '-' . uniqid() . '.' . $brochureFile2->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile2->move(
                        $this->getParameter('images_directory'),
                        $newFilename2
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $entreprise->setBrochureFilename($newFilename2);
            }
            $user = $this->getUser();
            $entreprise->setUpdatedBy($user);
            $entreprise->setUpdatedFromIp($this->GetIp());

            $entreprise->setUpdatedAt(new DateTime("now"));
            $this->addFlash('message', 'Modification avec succès');
            $entityManager->flush();

            return $this->redirectToRoute('app_entreprise_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('registration/editentreprise.html.twig', [
            'entreprise' => $entreprise,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/modifuser', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function editUser(Request $request, UserPasswordHasherInterface $userPasswordHasher, User $user, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        $form = $this->createForm(UpdateuserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $brochureFile2 = $form->get('brochure')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile2) {
                $originalFilename2 = pathinfo($brochureFile2->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename2 = $slugger->slug($originalFilename2);
                $newFilename2 = $safeFilename2 . '-' . uniqid() . '.' . $brochureFile2->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile2->move(
                        $this->getParameter('images_directory'),
                        $newFilename2
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $user->setBrochureFilename($newFilename2);
            }


            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $entityManager->persist($user);
            $this->addFlash('message', 'Modification avec succès');
            $entityManager->flush();

            return $this->redirectToRoute('app_registration_user', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('registration/update.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }


    
    #[Route('/profile/editpass', name: 'app_registration_editpass', methods: ['GET', 'POST'])]
    public function editPassAction(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $em)
   {
              // Recupere l'utilisateur courant
              $user = $this->getUser();

              if (null === $user) {
                  return $this->redirectToRoute('app_accueil');
              }
        if($request->isMethod('POST')){
           $user = $this->getUser();
        if($request->request->get('pass') == $request->request->get('pass2'));
        
          $user->setPassword($userPasswordHasher->hashPassword($user, $request->request->get('pass')));
           $em->flush();
            return $this->redirectToRoute('app_logout');
            $this->addFlash('success', 'Modification avec succès');
        } else {
               $this->addFlash(
                    'error',
                    'Les 2 mots de passe ne correspondent pas.'
                );  
        }
       return $this->render('profile/editpass.html.twig');     
           $em->flush();
           $this->addFlash('message', 'Changement de mot de passe avec succès');
    }
   
   
   

   #[Route('/editprofile', name: 'app_registration_editprofil', methods: ['GET', 'POST'])]
   public function editerProfilAction(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader)
   {
       // Recupere l'utilisateur courant
       $user = $this->getUser();

       if (null === $user) {
           return $this->redirectToRoute('app_accueil');
       }

       // Creation du formulaire d'edition
       $editUserForm = $this->createForm(EditProfileType::class, $user);

       // On indique au formulaire de prendre en charge le contenu de la requete
       // Il va mapper les different champs soumis avec le contenu de l entite $user
       $editUserForm->handleRequest($request);

       if ($request->isMethod('POST')) {
       if ($editUserForm->isSubmitted()) {
           $user = $this->getUser();
            
           $brochureFile = $editUserForm->get('photo')->getData();
           if ($brochureFile) {
               $brochureFileName = $fileUploader->upload($brochureFile);
               $user->setBrochureFilename($brochureFileName);
           }
                     // encode the plain password
       
//                $user->setUserConfirmationToken($token);
               $entityManager->persist($user);
               $entityManager->flush();

               // TO DO ENVOYER UN MAIL DE CONFIRMATION POUR ACTIVER LE COMPTE

               $this->addFlash(
                   'success',
                   'Vos modifications ont bien etes enregistrees.'
               );

               return $this->redirectToRoute('app_accueil');
           }
       }

       // Affichage
       return $this->render('profile/editprofile.html.twig', [
           'form' => $editUserForm->createView(),
           'libAction' => 'Modifier',
           'user' =>$user,
       ]);
   }
}
