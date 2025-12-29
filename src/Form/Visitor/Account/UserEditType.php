<?php

namespace App\Form\Visitor\Account;

use App\Dto\Visitor\User\UserEditDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UserEditType extends AbstractType
{
    public const array VALID_IMAGE_TYPES = ['jpeg', 'jpg', 'png', 'webp'];
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('avatar', FileType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'Avatar',
                'constraints' => [
                    new File(
                        maxSize: '5m',
                        extensions: self::VALID_IMAGE_TYPES,
                        extensionsMessage: 'Please upload a valid image file. Valid filestypes are:',
                    )
                ],
            ])
            ->add('userName', TextType::class)
            ->add('emailAddress', EmailType::class, [])
            ->add('submit', SubmitType::class, ['label' => 'Save Changes'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserEditDto::class,
        ]);
    }
}
