<?php

namespace App\Form;

use App\Entity\Depense;
use App\Entity\Objetdepense;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

class DepenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $objetdepense = $options['objetdepense'];
        $builder
            ->add('datedepense',  null, [
                'widget' => 'single_text',
               'required' => true,
               // this is actually the default format for single_text
               'format' => 'yyyy-MM-dd',
           ])
            ->add('montant')
            ->add('detail')
            ->add('beneficiaire')
            ->add('typedepense', ChoiceType::class, [
                'choices' => [
                    'Entrée' => 'Chèque',
                    'sortie' => 'Espèce',
                ],
                'placeholder' => '-- Choix Type Operation --',
                'required' => true,
            ])
            ->add('brochure', DropzoneType::class, [
                'required' => false,
                'mapped' => false,
                
                'attr' => [
                    'placeholder' => 'Glissez, deposez',
                ],
            ])
     
            ->add('objetdepense', EntityType::class, array(
                'choice_label' => function ($objetdepense) {
                    return $objetdepense->getLibelle();
                },
                'autocomplete' => true,
                'class' => Objetdepense::class,
                'choices' => $objetdepense,
                'multiple' => false,
                'required' => false,
                'placeholder' => '-- Choix  Objet depense --',
             
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
            'data_class' => Depense::class,
            'objetdepense' =>null,
        ]);
    }
}
