<?php

namespace App\Form\Game;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteGameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'confirm',
                CheckboxType::class,
                [
                    'label' => 'I really want to delete this game.',
                    'help' => 'Deleting this game will remove all players, puzzle instances and invitations from it. This action cannot be undone!',
                    'required' => true
                ]
            )
            ->add(
                'confirm_again',
                CheckboxType::class,
                [
                    'label' => 'I really, REALLY want to delete this game!',
                    'help' => 'Last chance to change your mind...',
                    'required' => true
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Delete this Game',
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
