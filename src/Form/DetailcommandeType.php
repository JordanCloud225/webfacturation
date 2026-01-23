<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Detailcommande;
use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class DetailcommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
         $builder
            ->add('type', HiddenType::class, [
                'data' => 'article', // Valeur par défaut
            ])
            ->add('quantite', IntegerType::class, [
                'attr' => ['class' => 'form-control quantite-input', 'min' => 1],
                'label' => false,
                'data' => 1,
            ]);

        // Événement pour gérer l'affichage conditionnel
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData(); // Votre entité
            
            if ($data === null) {
                // Mode création - afficher les deux champs
                $this->addBothFields($form);
                return;
            }

            // Vérifier si on a un article
            if ($data->getArticle() !== null) {
                $this->addArticleFieldOnly($form, $data);
                // Mettre à jour le type hidden
                $form->get('type')->setData('article');
            } 
            // Vérifier si on a un service
            elseif ($data->getService() !== null) {
                $this->addServiceFieldOnly($form, $data);
                // Mettre à jour le type hidden
                $form->get('type')->setData('service');
            } 
            else {
                // Ni l'un ni l'autre (cas rare) - afficher les deux
                $this->addBothFields($form);
            }
        });
    }

    private function addArticleFieldOnly($form, $data)
    {
        $form->add('article', EntityType::class, [
            'class' => Article::class,
            'choice_label' => 'libellefr',
            'placeholder' => 'Sélectionnez un article',
            'required' => false,
            'attr' => ['class' => 'form-control article-field'],
            'label' => false,
            'data' => $data->getArticle(), // Pré-remplir avec l'article existant
        ]);
        
        // Ne pas ajouter le champ service
    }

    private function addServiceFieldOnly($form, $data)
    {
        $form->add('service', EntityType::class, [
            'class' => Service::class,
            'choice_label' => 'libellefr',
            'placeholder' => 'Sélectionnez un service',
            'required' => false,
            'attr' => ['class' => 'form-control service-field'],
            'label' => false,
            'data' => $data->getService(), // Pré-remplir avec le service existant
        ]);
        
        // Ne pas ajouter le champ article
    }

    private function addBothFields($form)
    {
        $form
            ->add('article', EntityType::class, [
                'class' => Article::class,
                'choice_label' => 'libellefr',
                'placeholder' => 'Sélectionnez un article',
                'required' => false,
                'attr' => ['class' => 'form-control article-field'],
                'label' => false,
            ])
            ->add('service', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'libellefr',
                'placeholder' => 'Sélectionnez un service',
                'required' => false,
                'attr' => ['class' => 'form-control service-field'],
                'label' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Detailcommande::class,
            'total' => null, // Option to pass total if needed
            'prixunitaire' => null, // Option to pass unit price if needed
            'apply_custom_layout' => false,
        ]);
    }
}
