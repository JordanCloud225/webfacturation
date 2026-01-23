<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Boncommande;
use App\Entity\Detailcommande;
use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Detailcommande2Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
      ->add('articles', EntityType::class, [
                'class' => Article::class,
                'choice_label' => 'libellefr',
                'placeholder' => 'Sélectionnez un article',
                'required' => false,
                'attr' => ['class' => 'form-control article-field'],
                'label' => false,
        ])
        ->add('services', EntityType::class, [
            'class' => Service::class,
            'choice_label' => 'libellefr',
            'placeholder' => 'Sélectionnez un service',
            'required' => false,
            'attr' => ['class' => 'form-control service-field'],
            'label' => false,
        ])
        ->add('quantites', IntegerType::class, [
            'attr' => ['class' => 'form-control quantite-input', 'min' => 1],
            'label' => false,
            'data' => 1,
        ])
        ->add('prixunitaire', IntegerType::class,[
                'label' =>'Prix Unitaire :',
                'disabled' => true,
                'mapped' => false,
                'required' => false,
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Detailcommande::class,
            
        ]);
    }
}
