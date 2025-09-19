<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Game>
 */
class GameRepository extends ServiceEntityRepository
{

    private const string CHARACTERS = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    private const int SLUG_LENGTH = 16;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function getRandomUnusedSlug(): string {
        $randomString = '';

        for ($i = 0; $i < self::SLUG_LENGTH; $i++) {
            $randomString .= self::CHARACTERS[random_int(0, strlen(SELF::CHARACTERS) - 1)];
        }

        if (null !== $this->findOneBySlug($randomString)) {
            return $this->getRandomUnusedSlug();
        }
        return $randomString;
    }

    public function findOneBySlug(string $slug): ? Game {
        return $this->findOneBy([
            'slug' => $slug
        ]);
    }
}
