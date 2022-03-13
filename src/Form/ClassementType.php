<?php

namespace src\Form;

use App\Entity\Classement;
use App\Entity\Equipe;
use App\Entity\Evenement;
use App\Repository\ClassementRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClassementType extends AbstractType
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
            ->add('Rang', IntegerType::class)
            ->add('Ajouter',SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'event' => null,
        ));
    }

    public function getBlockPrefix()
    {
        return 'gestion_ranking';
    }
}
