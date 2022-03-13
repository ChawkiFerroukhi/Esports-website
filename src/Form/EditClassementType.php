<?php

namespace src\Form;

use App\Entity\Equipe;
use App\Entity\Evenement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditClassementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('equipe', EntityType::class, array(
                'class'=>Equipe::class,
                'choice_label'=>'nom',
                'multiple'=>false))
            ->add('evenement', EntityType::class, array(
                'class'=>Evenement::class,
                'choice_label'=>'nom',
                'multiple'=>false))
            ->add('Rang')
            ->add('Modifier',SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getBlockPrefix()
    {
        return 'gestion_shop_bundle_produits_type';
    }
}