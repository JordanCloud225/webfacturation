<?php

namespace App\Form;

use App\Entity\Fournisseur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

class FournisseurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', TextType::class, ['required' => true])
            ->add('contact', TextType::class, ['required' => true])
            ->add('email', EmailType::class, ['required' => true])
            ->add('adresse')
            ->add('langue', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'placeholder' => '-- Choix de la langue -',
                'expanded' => false,
                
                'choices' => [
                    'Français' => 'Français',
                    'English' => 'English',
                ],
            ]) 
            
            ->add('brochure', DropzoneType::class, [
                'required' => false,
                'mapped' => false,
                
                'attr' => [
                    'placeholder' => 'Glissez, deposez',
                ],
            ])
     
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
            'data_class' => Fournisseur::class,
        ]);
    }
}
