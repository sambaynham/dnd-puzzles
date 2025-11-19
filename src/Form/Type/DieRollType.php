<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DieRollType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        die("I WAS CALLED");
    }
}
