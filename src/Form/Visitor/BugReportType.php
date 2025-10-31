<?php

namespace App\Form\Visitor;

use App\Dto\Bugs\BugReportDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BugReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'reporterName',
                TextType::class,
                [
                    'label' => 'Your name',
                ])
            ->add(
                'reporterEmail',
                EmailType::class,
                [
                    'label' => 'Your e-mail address',
                ]
            )

            ->add(
                'summary',
                TextType::class,
            )
            ->add(
                'text',
                TextAreaType::class,
                [
                    'label' => 'What happened?',
                    'attr' => [
                        'rows' => 6,
                    ]
                ]
            )
            ->add(
                'referringUrl',
                TextType::class,
                [
                    'required' => false,
                    'attr' => [
                        'readonly' => true
                    ]

                ]
            )
            ->add('submit', SubmitType::class, [])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BugReportDto::class,
        ]);
    }
}
