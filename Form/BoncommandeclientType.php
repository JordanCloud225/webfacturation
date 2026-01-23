<?php

namespace App\Form;

use App\Entity\Boncommande;
use App\Entity\Boncommandeclient;
use App\Entity\Client;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

class BoncommandeclientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $boncommande = $options['boncommande'];
        $client = $options['client'];

        $builder
            ->add('datebdccli', DateType::class, [
                // renders it as a single text box
                'widget' => 'single_text',
                'required' => true,
                'mapped' => true,
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',
            ])
            ->add('numdevis', EntityType::class, array(
                'choice_label' => function ($boncommande) {
                    return $boncommande->getPo() ;
                },
                'autocomplete' => true,
                'class' => Boncommande::class,
                'choices' => $boncommande,
                'multiple' => false,
                'required' => true,
                'mapped' => false,
                'placeholder' => '-- Choix bon de commande --',
             
            ))
            ->add('brochure', DropzoneType::class, [
            'required' => true,
            'mapped' => false,
            
            'attr' => [
                'placeholder' => 'Glissez, deposez',
            ],
            ])
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
            'data_class' => Boncommandeclient::class,
            'boncommande' => NULL,
            'client' => NULL,
        ]);
    }
}
