<?php

namespace App\Form;

use App\Entity\Objetdepense;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BilanoperationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $objetdepense = $options['objetdepense'];
    $builder
       ->add('dateDebut', DateType::class, [
                // renders it as a single text box
                'widget' => 'single_text',
                'required' => true,
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',
                    ]
            )
            ->add('objetdepense', EntityType::class, array(
                'choice_label' => function ($objetdepense) {
                    return $objetdepense->getLibelle();
                },
                'autocomplete' => true,
                'class' => Objetdepense::class,
                'choices' => $objetdepense,
                'multiple' => false,
                'required' => false,
                'placeholder' => '-- Choix  objet --',
            
            ))
            ->add('dateFin', DateType::class, [
                // renders it as a single text box
                'widget' => 'single_text',
                'required' => true,
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',
                    ]
            )
    
            ->add('Rechercher', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'objetdepense' =>null,
        ]);
    }
}
