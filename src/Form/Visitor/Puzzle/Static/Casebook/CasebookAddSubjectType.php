<?php

declare(strict_types=1);

namespace App\Form\Visitor\Puzzle\Static\Casebook;

use App\Dto\Visitor\Puzzles\Static\Casebook\CasebookSubjectDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CasebookAddSubjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'help' => 'What is your Casebook subject\'s name?'
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'help' => 'Add a brief overview of this subject\'s background.'
                ]
            )
            ->add(
                'clues',
                CollectionType::class,
                [
                    'entry_type' => TextType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'prototype' => true,
                    'required' => false,
                    'prototype_options' => [
                        'required' => false,
                        'label' => 'Add a Clue',
                        'help' => 'Add the detail of this clue, to be revealed when the players discover it. For example; He was seen in the office just before the murder, or This gem burns with a yellow flame.',
                        'attr' => [
                            'placeholder' => 'Add a clue about this subject that you can reveal at-will.',
                        ]


                    ],
                    'entry_options' => [

                    ]
                ]
            )
            ->add(
                'add_entry',
                ButtonType::class,
                [
                    'label' => 'Add another clue',
                    'attr' => [
                        'class' => 'multiform-add-row-button'
                    ]
                ]
            )
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CasebookSubjectDto::class
        ]);
    }
}
