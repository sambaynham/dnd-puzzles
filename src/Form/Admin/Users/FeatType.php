<?php

namespace App\Form\Admin\Users;

use App\Dto\Admin\User\AdminFeatDto;
use App\Services\User\Domain\Permission;
use App\Services\User\Domain\Role;
use App\Services\User\Domain\UserFeat;
use App\Services\User\Domain\ValueObjects\Rarity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label')
            ->add('description')
            ->add('gamesMasterAwardable',
                CheckboxType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'handle',
            )
            ->add(
                'iconClass',
            )
            ->add(
                'rarity',
                ChoiceType::class,
                    [
                        'choices' => [
                           Rarity::makeFromRarityKey('c'),
                            Rarity::makeFromRarityKey('u'),
                            Rarity::makeFromRarityKey('r'),
                            Rarity::makeFromRarityKey('e'),
                            Rarity::makeFromRarityKey('l')
                        ],
                        'choice_value' => 'key',
                        'choice_label' => 'label',
                    ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Save Changes',
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AdminFeatDto::class,
        ]);
    }
}
