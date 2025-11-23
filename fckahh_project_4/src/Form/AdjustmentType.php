<?php

namespace App\Form;

use App\Entity\Accrual;
use App\Entity\Adjustment;
use App\Entity\Deduction;
use App\Entity\Employee;
use App\Entity\SalaryCalculation;
use App\Entity\TaxRule;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdjustmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('changedFieldName')
            ->add('oldValue')
            ->add('newValue')
            ->add('changeReason')
            ->add('changedBy')
            ->add('changedAt')
            ->add('employee', EntityType::class, [
                'class' => Employee::class,
                'choice_label' => 'id',
            ])
            ->add('accrual', EntityType::class, [
                'class' => Accrual::class,
                'choice_label' => 'id',
            ])
            ->add('deduction', EntityType::class, [
                'class' => Deduction::class,
                'choice_label' => 'id',
            ])
            ->add('salaryCalculation', EntityType::class, [
                'class' => SalaryCalculation::class,
                'choice_label' => 'id',
            ])
            ->add('taxRule', EntityType::class, [
                'class' => TaxRule::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adjustment::class,
        ]);
    }
}
