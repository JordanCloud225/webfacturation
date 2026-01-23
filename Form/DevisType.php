<?php

namespace App\Form;

use App\Entity\Boncommande;
use App\Entity\Client;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DevisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $client = $options['client'];
        $tva = $options['tva'];
        $builder
          
        ->add('po', TextType::class, [
            'required' => false
        ])     
        ->add('jobtitle', TextType::class, [
            'required' => false
        ])      
        ->add('sitelocation', TextType::class, [
            'required' => false
        ])       
      
       ->add('datedevis', DateType::class, [
        // renders it as a single text box
        'widget' => 'single_text',
        'required' => true,
        // this is actually the default format for single_text
        'format' => 'yyyy-MM-dd',
        'data' => new \DateTime(),
            ])
                 
        ->add('client', EntityType::class, array(
            'choice_label' => function ($client) {
                return $client->getLibelle();
            },
            'autocomplete' => true,
            'class' => Client::class,
            'choices' => $client,
            'multiple' => false,
            'required' => true,
            'placeholder' => '-- Choix  client --',
            
        ))

        ->add('type', ChoiceType::class, [
                'required' => true,
                'mapped' => false,
                'placeholder' => '-- Choix  TVA --',
                'choices' => [
                    'Non' => '0',
                    'Oui' => '1',
                ],
                'data' => (isset($options['tva']) && $options['tva'] != 0) ? '1' : '0',
            ])

        ->add('detailcommandes', CollectionType::class, [
            'entry_type' => DetailcommandeType::class,
            'label' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'entry_options' => ['label' => false],
            // 'attr' => [
            //     'data-controller' => 'form-collection'
            // ]
        ])
                   // Fin champs annexes 
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
            'data_class' => Boncommande::class,
            'client' =>null,
            'tva' => null,
            'apply_custom_layout' => true,
        ]);
    }
}
