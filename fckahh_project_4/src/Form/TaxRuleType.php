<?php

namespace App\Form;

use App\Entity\TaxRule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class TaxRuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('minAmount', NumberType::class,[
                'constraints' => [
                    new Range(['min' => 0,'minMessage'=>'Cannot be negative!'])
                ]
            ])
            ->add('maxAmount', NumberType::class,[
                'required' => false,
                'attr' => ['placeholder' => 'Live blank for "..and higher"']
            ])
            ->add('rate', NumberType::class,[
                'scale' => 2,
                'constraints' => [
                    new Range([
                        'min' => 0,
                        'max' => 100,
                        'notInRangeMessage' => 'Rate must be between 0 and 100!'
                    ])
                ]
            ])
            ->add('taxType', ChoiceType::class,[
                'choices' => [
                    'Income tax' => 'INCOME_TAX',
                    'Pension' => 'PENSION',
                    'Social' => 'SOCIAL'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TaxRule::class,
        ]);
    }
}
