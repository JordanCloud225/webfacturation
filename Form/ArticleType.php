<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Fabricant;
use App\Entity\Marque;
use App\Entity\Typearticle;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $typearticle = $options['typearticle'];
        $fabricant = $options['fabricant'];
        $marque = $options['marque'];
        $builder
            ->add('libellefr', TextType::class, [
                'required'=>false,
            ])
            ->add('quantitestock')
            ->add('reference')
            ->add('quantitelimite')
            ->add('prixunitaire', null, [
                'required' => true,
            ])
            ->add('brochure', DropzoneType::class, [
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Vous pouvez Glissez, deposez',
                ],
            ])
 
            ->add('marque', EntityType::class, array(
                'choice_label' => function ($marque) {
                    return $marque->getLibellefr();
                },
                'autocomplete' => true,
                'class' => Marque::class,
                'choices' => $marque,
                'multiple' => false,
                'required' => false,
                'placeholder' => '-- Choix  Marque --',
             
            ))
             
      
            ->add('fabricant', EntityType::class, array(
                'choice_label' => function ($fabricant) {
                    return $fabricant->getLibellefr();
                },
                'autocomplete' => true,
                'class' => Fabricant::class,
                'choices' => $fabricant,
                'multiple' => false,
                'required' => false,
                'placeholder' => '-- Choix  Fabricant --',
             
            ))
            ->add('typearticle', EntityType::class, array(
                'choice_label' => function ($typearticle) {
                    return $typearticle->getLibellefr() ;
                },
                'autocomplete' => true,
                'class' => Typearticle::class,
                'choices' => $typearticle,
                'multiple' => false,
                'required' => false,
                'placeholder' => '-- Choix  Type --',
             
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
            'data_class' => Article::class,
            'typearticle'=>null,
            'marque'=>null,
            'fabricant'=>null,
        ]);
    }
}
