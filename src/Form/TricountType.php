<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Tricount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TricountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('device')
            ->add('content')
            ->add('createdBy', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name',
            ])
            ->add('participants', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tricount::class,
        ]);
    }
}
