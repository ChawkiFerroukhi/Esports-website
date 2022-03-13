<?php

namespace App\Form;
use App\Entity\Categories;
use App\Entity\Reclamation;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sujet')
            ->add('category', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => function($nom){
                    return $nom->getName();
                },
                
            ])
            ->add('email')
            ->add('description')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => function($nom){
                    return $nom->getUsername();
                },
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
