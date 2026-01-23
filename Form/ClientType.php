<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('libelle', TextType::class, ['required' => true ])
        ->add('siren', TextType::class, ['required' => false ])
        ->add('codenaf', TextType::class, ['required' => false ])
        ->add('numtv', TextType::class, ['required' => false ])
        ->add('adresse', TextType::class, ['required' => false ])
        ->add('sigle', TextType::class, ['required' => false ])
        ->add('complementadresse', TextType::class, ['required' => false ])
        ->add('ville', TextType::class, ['required' => true, ])
        ->add('pays', CountryType::class, array( 'label' => 'Pays de naissance*',
        'preferred_choices' => array('CI'),
        'mapped'=>true,
        'required' => true,
        'choice_translation_locale' => null))
        ->add('contact1', TextType::class, ['required' => false ])
        ->add('contact2', TextType::class, ['required' => false ])
        ->add('email', EmailType::class, ['required' => false ])
        ->add('codepostal', TextType::class, ['required' => false ])
        ->add('langue', ChoiceType::class, [
            'multiple' => false,
            'required' => false,
            'expanded' => false,
            'placeholder' => ' -- Choix de la Langue -- ',
            'choices' => [
                'Français' => 'Français',
                'English' => 'English',
            ],
        ]) 
      
        ->add('sitweb', TextType::class, ['required' => false ])
 
           
         
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
            'data_class' => Client::class,
        ]);
    }
}
