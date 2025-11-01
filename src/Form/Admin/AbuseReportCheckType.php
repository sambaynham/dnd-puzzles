<?php

namespace App\Form\Admin;

use App\Dto\Admin\Abuse\AbuseReportCheckDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbuseReportCheckType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'reportConfirmed',
            CheckboxType::class,
                [
                    'required' => false,
                    'help' => 'Check this box if you believe the report to be genuine. This will add a strike to the reported user\'s account, possibly resulting in a ban.'
                ]
            )
            ->add(
                'reportReporter',
                CheckboxType::class,
                [
                    'required' => false,
                    'help' => 'Check this box if you believe the report to be malicious. This will add a strike to the reporting user\'s account, possibly resulting in a ban.'
                ]

            )
            ->add('submit', SubmitType::class, [
                'label' => 'Mark Report Checked',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AbuseReportCheckDto::class
        ]);
    }
}
