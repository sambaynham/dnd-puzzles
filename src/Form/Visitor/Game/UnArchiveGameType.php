<?php

namespace App\Form\Visitor\Game;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UnArchiveGameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'confirm',
                CheckboxType::class,
                [
                    'label' => 'I really want to unarchive this game.',
                    'help' => 'Archiving a game will make it playable again.',
                    'required' => true
                ]
            )

            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Unarchive this Game',
                    'attr'=> [
                        'class' => 'btn-success'
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
