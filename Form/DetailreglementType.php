<?php

namespace App\Form;

use App\Entity\Detailreglement;
use App\Entity\Reglement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DetailreglementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montantpaye')
            ->add('datepaiement', null, [
                'widget' => 'single_text',
            ])
            ->add('identreprise')
            ->add('reste')
            ->add('createdAt', null, [
                'widget' => 'single_text',
            ])
            ->add('updatedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('deletedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('createdBy')
            ->add('updatedBy')
            ->add('deletedBy')
            ->add('reglement', EntityType::class, [
                'class' => Reglement::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Detailreglement::class,
        ]);
    }
}
