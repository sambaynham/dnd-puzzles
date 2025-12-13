<?php

declare(strict_types=1);

namespace App\Services\Puzzle\Domain\Casebook;

use App\Services\Core\Domain\AbstractValueObject;
use App\Services\Puzzle\Infrastructure\Casebook\CasebookSubjectTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CasebookSubjectTypeRepository::class)]
class CasebookSubjectType extends AbstractValueObject
{
}
