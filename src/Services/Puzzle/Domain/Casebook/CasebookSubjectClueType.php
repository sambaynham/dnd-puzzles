<?php

namespace App\Services\Puzzle\Domain\Casebook;

use App\Services\Core\Domain\AbstractValueObject;
use App\Services\Puzzle\Infrastructure\Casebook\CasebookSubjectClueTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CasebookSubjectClueTypeRepository::class)]
class CasebookSubjectClueType extends AbstractValueObject
{
    public static function hasDescription(): bool
    {
        return false;
    }
}
