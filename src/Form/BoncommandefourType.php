<?php

namespace App\Form;

use App\Entity\Boncommandefour;
use App\Entity\Fournisseur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

class BoncommandefourType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $fournisseur = $options['fournisseur'];
        $builder
            ->add('brochure', DropzoneType::class, [
            'required' => true,
            'mapped' => false,
            'attr' => [
                'placeholder' => 'Glissez, deposez',
            ],
            ])
            ->add('datebdc', DateType::class, [
                // renders it as a single text box
                'widget' => 'single_text',
                'required' => true,
                'mapped' => true,
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',
            ])
            ->add('fournisseur', EntityType::class, array(
                'choice_label' => function ($fournisseur) {
                    return $fournisseur->getLibelle();
                },
                'autocomplete' => true,
                'class' => Fournisseur::class,
                'choices' => $fournisseur,
                'multiple' => false,
                'required' => false,
                'placeholder' => '-- Choix  Fournisseur --', 
            ))
             ->add('save', SubmitType::class, [
                'attr' => [
                    'value' => 'save'
                ]
            ])  
            ->add('saveAndAdd', SubmitType::class, [
                'attr' => [
                    'value' => 'save-and-add'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Boncommandefour::class,
            'fournisseur' => NULL,
        ]);
    }
}
