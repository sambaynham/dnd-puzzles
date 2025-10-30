<?php

namespace App\Form\Game\Invitations;

use App\Dto\Game\Invitations\InvitePlayerDto;
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
                'email',
                EmailType::class,
                [
                    'required' => true,
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
