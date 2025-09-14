<?php

namespace App\Form;

use App\Dto\Game\CreateGameDto;
use App\Entity\Game;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateGameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'help' => 'Give your game a short, punchy, memorable name.'
                ]
            )
            ->add(
                'slug',
                TextType::class,
                [
                    'help' => 'This is the URL of your game. It will be used to access your game. It may only contain lowercase letters, numbers and underscores.'
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'help' => 'Describe your game here! Max 1024 characters.',
                    'attr' => [
                        'rows' => 10
                    ]
                ]
            )
            ->add('submit', SubmitType::class, ['label' => 'Create game'])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreateGameDto::class,
        ]);
    }
}
