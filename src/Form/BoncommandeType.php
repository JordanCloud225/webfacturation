<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Boncommande;
use App\Entity\Client;
use App\Entity\Conditionoffre;
use App\Form\DetailcommandeType;
use App\Form\Detailcommande2Type;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BoncommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $client = $options['client'];
        $builder

        ->add('po', TextType::class, [
            'required' => false,
        ])      ->add('jobtitle', TextType::class, [
            'required' => false
        ])      ->add('sitelocation', TextType::class, [
            'required' => false
        ])
        ->add('montantremise')

            // ->add('montantremise')
             ->add('type', ChoiceType::class, [
                'required' => true,
                'mapped' => false,
                'placeholder' => '-- Choix  TVA --',
                'choices' => [
                    'Non' => '0',
            'Oui' => '1',
                 ],
            ])

            ->add('datecommande', DateType::class, [
                // renders it as a single text box
                'widget' => 'single_text',
                'required' => true,
                'mapped' => true,
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',
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

            // Fin champs annexes 
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
            'data_class' => Boncommande::class,
            'client' => null,
        ]);
    }
}
