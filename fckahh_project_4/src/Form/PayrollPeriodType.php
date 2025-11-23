<?php

namespace App\Form;

use App\Entity\PayrollPeriod;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;

class PayrollPeriodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateType::class,[
                'widget' => 'single_text'
            ])
            ->add('endDate',DateType::class,[
                'widget' => 'single_text',
                'constraints' =>[
                    new GreaterThan([
                        'propertyPath' => 'parent.all[startDate].data',
                        'message' => 'End date must be later than start date!'
                    ])
                ]
            ])
            ->add('status', ChoiceType::class,[
                'choices' => [
                    'Draft' => 'DRAFT',
                    'Approved' => 'APPROVED',
                    'Processed' => 'PROCESSED'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PayrollPeriod::class,
        ]);
    }
}
