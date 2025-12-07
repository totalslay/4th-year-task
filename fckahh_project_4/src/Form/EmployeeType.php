<?php

namespace App\Form;

use App\Entity\Employee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class EmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => 'Full name',
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u',
                        'message' => 'Only letters, spaces and dashes',
                    ]),
                ],
            ])
            ->add('TIN', TextType::class, [
                'label' => 'TIN',
                'constraints' => [
                    new Length([
                        'min' => 12,
                        'max' => 12,
                        'exactMessage' => 'TIN should contain exactly 12 numbers',
                    ]),
                    new Regex([
                        'pattern' => '/^\d+$/',
                        'message' => 'Only numbers',
                    ]),
                ],
            ])
            ->add('bankAccount', TextType::class, [
                'label' => 'Bank Account',
                'constraints' => [
                    new Length([
                        'min' => 20,
                        'max' => 20,
                        'exactMessage' => 'Bank Account should contain exactly 20 numbers',
                    ]),
                    new Regex([
                        'pattern' => '/^\d+$/',
                        'message' => 'Only numbers',
                    ]),
                ],
            ])
            ->add('employmentType', ChoiceType::class, [
                'label' => 'employment type',
                'choices' => [
                    'Full time' => 'FULL_TIME',
                    'Part time' => 'PART_TIME',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Choose an option!']),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employee::class,
        ]);
    }
}
