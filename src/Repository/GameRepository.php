<?php

namespace App\Repository;

use App\Entity\Game;
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

    public function getRandomUnusedSlug(): string {

        $randomSlug = CodeGenerator::generateRandomCode();
        if (null !== $this->findOneBySlug($randomSlug)) {
            return $this->getRandomUnusedSlug();
        }
        return $randomSlug;
    }

    public function findOneBySlug(string $slug): ? Game {
        return $this->findOneBy([
            'slug' => $slug
        ]);
    }
}
