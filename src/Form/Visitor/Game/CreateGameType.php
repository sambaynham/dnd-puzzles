<?php

namespace App\Form\Visitor\Game;

use App\Dto\Game\CreateGameDto;
use App\Repository\GameRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateGameType extends AbstractType
{
    public function __construct(private GameRepository $gameRepository) {

    }
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
                HiddenType::class,
                [
                    'attr' => [

                        'value' => $this->gameRepository->getRandomUnusedSlug()
                    ],
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
