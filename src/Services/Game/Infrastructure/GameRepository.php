<?php

namespace App\Services\Game\Infrastructure;

use App\Services\Game\Domain\Game;
use App\Services\Puzzle\Infrastructure\CodeGenerator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Game>
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function findOneBySlug(string $slug): ? Game {
        return $this->findOneBy([
            'slug' => $slug
        ]);
    }
}
