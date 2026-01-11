<?php

namespace App\Form\Visitor\Game;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArchiveGameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'confirm',
                CheckboxType::class,
                [
                    'label' => 'I really want to archive this game.',
                    'help' => 'Archiving a game will render it unplayable until it is unarchived.',
                    'required' => true
                ]
            )

            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Archive this Game',
                    'attr'=> [
                        'class' => 'btn-danger'
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
