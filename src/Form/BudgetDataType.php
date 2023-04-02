<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BudgetDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'data',
                TextareaType::class,
                [
                    'attr' => [
                        'rows' => 5,
                        'cols' => 40,
                    ]
                ]
            )
        ;
        if (null!=$options['BudgetYear']) {
            $builder->add(
                'BudgetYearID',
                HiddenType::class,
                [
                    'data' => $options['BudgetYear']->getId()
                ]
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'BudgetYear' => null,
        ]);
    }
}
