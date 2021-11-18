<?php

namespace App\Form;

use App\Entity\Useraccount;
use App\Entity\Expense;
use App\Entity\Tricount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ExpenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('createdAt')
            ->add('amount')
            /* ->add('tricount', HiddenType::class, [
                #'class' => Tricount::class,
                'data' => 22
            ]) */
            ->add('userPaid', EntityType::class, [
                'class' => Useraccount::class,
                'choice_label' => 'name',
            ])
            ->add('userRefund', EntityType::class, [
                'class' => Useraccount::class,
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
