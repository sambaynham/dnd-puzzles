<?php

namespace App\Form;

use App\Dto\Game\InvitePlayerDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvitePlayerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add(
                'emailOne',
                EmailType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'emailTwo',
                EmailType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'emailThree',
                EmailType::class,
                [
                    'required' => false,
                    'help' => '<p>Enter your player\'s email address. If they are not already a member, they will be invited to join.</p><p>You can invite up to three players at a time.</p><br><p><strong>Important Note:</strong> Players have the option to refuse your invitation, and also to block their e-mail addresses from being invited again. If this happens too many times, your account will be banned!</strong><br><em>Do not use this form to spam people!</em></em></p>',
                    'help_html' => true
                ]

            )
            ->add(
                'invitationCode',
                TextType::class,
                [
                    'help' => 'Your players will use this code to join your game. It expires after 24 hours.',
                    'attr' => [
                        'readonly'=>true,
                    ]
                ])
            ->add('invitationText', TextType::class, [
                'attr' => [
                    'max'=> 255
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Invite players',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InvitePlayerDto::class,
        ]);
    }
}
