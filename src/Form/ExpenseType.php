<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Expense;
use App\Entity\Tricount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('createdAt')
            ->add('amount')
            ->add('tricount', EntityType::class, [
                'class' => Tricount::class,
                'choice_label' => 'name',
            ])
            ->add('userPaid', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name',
            ])
            ->add('userRefund', EntityType::class, [
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
            'data_class' => Expense::class,
        ]);
    }
}
