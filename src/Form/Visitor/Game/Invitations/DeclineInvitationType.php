<?php

declare(strict_types=1);

namespace App\Form\Visitor\Game\Invitations;

use App\Dto\Visitor\Game\Invitations\DeclineInvitationDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeclineInvitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'reason',
                ChoiceType::class,
                [
                    'expanded' => false,
                    'multiple' => false,
                    'choices' => [
                        'Received in Error' => 'received_in_error',
                        'Unknown Sender' => 'unknown_sender',
                        'Abusive Request' => 'abuse',
                        'Other' => 'other',
                    ],
                    'help' => 'Please state why you are declining this request. If it\'s an innocent mistake, please select \'Received in Error\'. Other reasons may result in blocking a sender\'s account.',
                ]
            )
            ->add(
                'notes',
                TextareaType::class,
                [
                    'attr' => [
                        'rows' => 5,
                        'cols' => 50,
                    ],
                    'required' => false,
                    'help' => 'Please add any notes you would like taken into consideration.'
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Decline Invitation',
                    'attr' => [
                        'class' => 'btn-danger'
                    ],

                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DeclineInvitationDto::class,
        ]);
    }
}
