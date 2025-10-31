<?php

declare(strict_types=1);

namespace App\Form\Visitor;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                '_username',
                EmailType::class,
                [
                    'label' => 'Email',
                    'required' => true,
                    'attr' => [
                        'name' => '_username'
                    ]
                ]
            )
            ->add(
                '_password',
                PasswordType::class,
                [
                    'required' => true,
                    'attr' => [
                        'name' => '_password'
                    ]
                ]
            )
            ->add(
                'submit',
                SubmitType::class
            );
        ;
    }

    public function getBlockPrefix()
    {
        return '';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

