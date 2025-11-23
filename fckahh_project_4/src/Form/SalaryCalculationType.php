<?php

namespace App\Form;

use App\Entity\PayrollPeriod;
use App\Entity\SalaryCalculation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SalaryCalculationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('grossAmount')
            ->add('netAmount')
            ->add('period', EntityType::class, [
                'class' => PayrollPeriod::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SalaryCalculation::class,
        ]);
    }
}
