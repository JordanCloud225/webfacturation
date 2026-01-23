<?php

namespace App\Form;

use App\Entity\Conditionoffre;
use App\Entity\Detailcondition;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DetailConditionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('conditionoffre', EntityType::class, [
                'class' => Conditionoffre::class,
                'choice_label' => 'libelle',
                'label' => 'Condition d\'offre',
                'placeholder' => '-- Condition d\'offre --',
                'attr' => ['class' => 'conditionoffre-field form-control'],
                'required' => true
            ])
            
           ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Detailcondition::class
        ]);
    }
}
