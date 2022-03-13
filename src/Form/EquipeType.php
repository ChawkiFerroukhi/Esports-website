<?php

namespace App\Form;

use App\Entity\Jeux;
use App\Entity\Equipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('evenements')
            ->add('classement')
            ->add('Jeux', EntityType::class, [
                'class' => Jeux::class,
                'choice_label' => function($nom) {
                    return $nom->getNom();
                },
                'required' => false,
                    'multiple' => true,

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipe::class,
        ]);
    }
}