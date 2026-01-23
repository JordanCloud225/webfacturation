<?php

namespace App\Form;

use App\Entity\Entreprise;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntrepriseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle')
            ->add('siren')
            ->add('codenaf')
            ->add('competance')
            ->add('numtva')
            ->add('adresse')
            ->add('complementadresse')
            ->add('ville')
            ->add('pays')
            ->add('contact1')
            ->add('contact2')
            ->add('email')
            ->add('codepostal')
            ->add('langue')
            ->add('siteweb')
            ->add('brochureFilename')
      
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Entreprise::class,
        ]);
    }
}
