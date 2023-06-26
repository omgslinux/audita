<?php

namespace App\Form;

use App\Entity\BudgetYear;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BudgetYearType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add(
            'year',
            null,
            [
                'widget' => 'single_text',
                'label' => 'Año',
            ]
        )
        ->add('ipcVariation')
        ->add('initialBudget')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BudgetYear::class,
        ]);
    }
}
