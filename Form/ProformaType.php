<?php

namespace App\Form;

use App\Entity\Boncommande;
use App\Entity\Client;
use App\Entity\Conditionoffre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProformaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $client = $options['client'];
        $builder

            ->add('dateproforma',  null, [
                'widget' => 'single_text',
                'required' => true,
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',
            ])
     
            ->add('po', TextType::class, [
                'required' => false
            ])      ->add('jobtitle', TextType::class, [
                'required' => false
            ])      ->add('sitelocation', TextType::class, [
                'required' => false
            ])
          
            // ->add('type', ChoiceType::class, [
            //     'required' => true,
            //     'mapped' => false,
            //     'placeholder' => '-- Choix  TVA --',
            //     'choices' => [
            //         'Non' => '0',
            //         'Oui' => '1',
            //     ],
            // ])
            ->add('montantremise')
            ->add('delivraydelai')

            ->add('client', EntityType::class, array(
                'choice_label' => function ($client) {
                    return $client->getLibelle();
                },
                'autocomplete' => true,
                'class' => Client::class,
                'choices' => $client,
                'multiple' => false,
                'required' => false,
                'placeholder' => '-- Choix  client --',
             
            ))

            ->add('conditionoffre', EntityType::class, [
            'choice_label' => 'Libelle',
            'class' => Conditionoffre::class,
            'placeholder' =>'--Choix de la conditon',
            'multiple' => false,
            'required' => false,
            'autocomplete' => true,
        ])

            // ->add('detailcommandes', CollectionType::class, [
            //     'entry_type' => DetailcommandeType::class,
            //     'label' => false,
            //     'entry_options' => [
            //         'label' => false,
            //         'apply_custom_layout' => false,
            //     ],
            //     'allow_add' => true,
            //     'allow_delete' => true,
            //     'by_reference' => false,
            //     'prototype' => true,

            // ])
            // ->add('save', SubmitType::class, [
            //     'attr' => [
            //         'value' => 'save'
            //     ]
            // ])  
        
            ;
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Boncommande::class,
            'client' =>null,
            'apply_custom_layout' => false,
        ]);
    }
}
