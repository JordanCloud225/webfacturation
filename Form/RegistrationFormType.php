<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\UX\Dropzone\Form\DropzoneType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

 
        ->add('libelle', TextType::class, ['required' => true, 'mapped' => false,])
        ->add('siren', TextType::class, ['required' => false, 'mapped' => false,])
        ->add('competance', TextType::class, ['required' => false, 'mapped' => false,])
        ->add('codenaf', TextType::class, ['required' => false, 'mapped' => false,])
        ->add('numtva', TextType::class, ['required' => false, 'mapped' => false,])
        ->add('adresse', TextType::class, ['required' => false, 'mapped' => false,])
        ->add('sigle', TextType::class, ['required' => false, 'mapped' => false,])
        ->add('complementadresse', TextType::class, ['required' => false, 'mapped' => false,])
        ->add('ville', TextType::class, ['required' => true, 'mapped' => false,])
        ->add('pays', CountryType::class, array( 'label' => 'Pays de naissance*',
        'preferred_choices' => array('CI'),
        'mapped'=>false,
        'choice_translation_locale' => null
        ))
        ->add('contact1', TextType::class, ['required' => true, 'mapped' => false,])
        ->add('contact2', TextType::class, ['required' => false, 'mapped' => false,])
        ->add('email1', EmailType::class, ['required' => true, 'mapped' => false,])
        ->add('codepostal', TextType::class, ['required' => false, 'mapped' => false,])
        ->add('langue', ChoiceType::class, [
            'required' => true,
            'multiple' => false,
            'expanded' => false,
            'mapped'=>false,
            'choices' => [
                'Français' => 'Français',
                'English' => 'English',
            ],
        ]) 
      
        ->add('siteweb', TextType::class, ['required' => false, 'mapped' => false,])
        ->add('brochure2', DropzoneType::class, [
            'required' => false,
            'mapped' => false,
            'attr' => [
                'placeholder' => 'Glissez et deposez',
            ],
        ])
        
      
        ->add('contact')
        ->add('nom', TextType::class, [
            'required' => false,
            'constraints' => [
                new Regex([
                    'pattern' => '/^[0-9a-zA-Z-\s\'ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]+$/',
                    'match' => true,
                    'message' => 'sont seulement acceptés: les chiffres, les lettres minuscules et majuscules avec ou sans accents, les espaces, les tirets et les apostrophes',
                        ])
            ],
        ]) 
        ->add('roles', ChoiceType::class, [
            'required' => true,
            'multiple' => false,
            'expanded' => false,
            'choices' => [
                'Administrateur' => 'ROLE_ADMIN',
            ],
        ]) 

        ->add('brochure', DropzoneType::class, [
            'required' => false,
            'mapped' => false,
            'attr' => [
                'placeholder' => 'Glissez, deposez',
            ],
        ])
 

            ->add('email')
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation mot de passe'],
                'invalid_message' => 'Les champs mot de passe doivent être identiques',
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'Mot de passe doit comporter 4 caractères au moins',
                            ]),
                ],
            ])
        ;
        
        $builder->get('roles')
                ->addModelTransformer(new CallbackTransformer(
                                function ($rolesArray) {
// transform the array to a string
                                    return count($rolesArray) ? $rolesArray[0] : null;
                                },
                                function ($rolesString) {
// transform the string back to an array
                                    return [$rolesString];
                                }
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
