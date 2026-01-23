<?php

namespace App\Form;

use App\Entity\Service;
use App\Entity\Typeservice;
use App\Repository\TypeserviceRepository;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $typeservice = $options['typeservice'];
        $builder
            ->add('libellefr', TextType::class, [
                'required'=>true,
            ])
            ->add('prixunitaire', null, [
                'required' => true,
            ])
         
            ->add('typeservice', EntityType::class, array(
              
                'class' => Typeservice::class,
                'choice_label'=>'libellefr',
                'multiple' => false,
                'required' => true,
                'query_builder' => function (TypeserviceRepository $er) use($typeservice){
                    return $er->createQueryBuilder('c');
                    $er->where('c.deletedAt is NULL');
                    $er->andWhere('c.identreprise = :typeservice');
                    $er->setParameter('typeservice', $typeservice);
                    $er->orderBy('c.id', 'ASC');
                },
                'placeholder' => '-- Choix du type service --',
              
            ))
            // ->add('typeservice', EntityType::class, [
            //     'class' => Typeservice::class,
            //     'choice_label' => 'libellefr'  ,
            //     'placeholder' => '-- Choix du typeservice --',
            // ])
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
            'data_class' => Service::class,
            'typeservice'=>null,
        ]);
    }
}

