<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Services\Puzzle\Domain\Casebook\CasebookSubjectClueType;
use Predis\Command\Argument\Search\SchemaFields\TextField;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ClueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void {



        $builder->add(
            'clueName',
            TextType::class,
            [
                'help' => 'Enter a short name for your clue.'
            ]
        )
        ->add(
            'clueDescription',
            TextareaType::class,
            [
                'help' => 'Enter the detailed content of the clue'
            ]
        )
        ->add(
            'clueType',
            EntityType::class,
            [
                'class' => CasebookSubjectClueType::class,
                'choice_label' => 'name',
                'help' => 'Select a type for your clue.'
            ]
        );

    }
}
