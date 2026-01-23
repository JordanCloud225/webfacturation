<?php

namespace App\Form;

use App\Entity\Facture;
use App\Entity\Reglement;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReglementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('montant')
        ->add('montantverse', IntegerType::class, [
            'mapped' => false,
        ])
        ->add('reste')
        ->add('datereglement',  null, [
                'widget' => 'single_text',
                'required' => true,
                'mapped' => false,
                // this is actually the default format for single_text
               'format' => 'yyyy-MM-dd',
           ])

           ->add('mode', ChoiceType::class, [
            'required' => true,
            'multiple' => false,
            'placeholder' =>'-- Mode reglement --', 
            'expanded' => false,
            'choices' => [
                'Espece' => 'Espece',
                'Virement bancaire' => 'Virement bancaire',
                ' Account' => 'Account',
                'Depot' => 'Account',
            ],
        ])
       
            ->add('facture', EntityType::class, [
                'class' => Facture::class,
                'choice_label' => 'id',
            ])
            ->add('save', SubmitType::class, [
                'attr' => [
                    'value' => 'save'
                ]
            ])  
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reglement::class,
        ]);
    }
}
