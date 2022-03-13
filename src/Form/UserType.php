<?php

namespace App\Form;

use App\Entity\Departement;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, array(
                'error_bubbling' => true,
                )
            )
            ->add('prenom')
            ->add('email')
            ->add('phone')
            ->add('password', PasswordType::class)
            ->add('username')
            ->add('Departement', EntityType::class, [
                'class' => Departement::class,
                'choice_label' => function($nom){
                    return $nom->getNom();
                },
            
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
