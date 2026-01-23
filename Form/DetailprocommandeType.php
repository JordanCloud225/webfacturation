<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Detailcommande;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DetailprocommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
         
            ->add('prixunitaire')
            ->add('quantite')
     
            ->add('article', EntityType::class, [
                'class' => Article::class,
                'required'=>true,
                'autocomplete' => true,
                'choice_label' => 'libellefr',
                'placeholder' => '-- Choix article --',
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
