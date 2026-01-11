<?php

namespace App\Form\Admin\Users;

use App\Dto\Visitor\User\UserBlockDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminDeleteUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'confirm',
                CheckboxType::class,
                [
                    'required' => true,
                    'label' => 'I really want to delete this user.'
                ]
            )
            ->add(
                'confirm_confirm',
                CheckboxType::class,
                [
                    'required' => true,
                    'label' => 'I\'m sure I want to delete this user.'
                ]
            )
            ->add('submit', SubmitType::class, [
                'label' => 'Delete User',
                'attr' => [
                    'class' => 'btn btn-danger',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([

        ]);
    }
}
