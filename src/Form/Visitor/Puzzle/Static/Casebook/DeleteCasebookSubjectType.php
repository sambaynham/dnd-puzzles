<?php

declare(strict_types=1);

namespace App\Form\Visitor\Puzzle\Static\Casebook;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteCasebookSubjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'confirm',
                CheckboxType::class,
                [
                    'label' => 'Really delete subject?',
                    'help' => 'This action cannot be undone!'
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Delete',
                    'attr' => [
                        'class' => 'btn btn-danger'
                    ]
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
