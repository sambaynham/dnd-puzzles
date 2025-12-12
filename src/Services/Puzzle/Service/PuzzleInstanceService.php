<?php

declare(strict_types=1);

namespace App\Services\Puzzle\Service;

use App\Services\Game\Domain\Game;
use App\Services\Puzzle\Domain\Exceptions\MismappedPuzzleTemplateException;
use App\Services\Puzzle\Domain\Interfaces\PuzzleInstanceInterface;
use App\Services\Puzzle\Domain\Interfaces\StaticPuzzleInstanceInterface;
use App\Services\Puzzle\Service\Exceptions\TemplateNotFoundException;
use App\Services\Puzzle\Service\Interfaces\PuzzleInstanceServiceInterface;
use App\Services\Puzzle\Service\Interfaces\PuzzleTemplateRegistryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use App\Services\Puzzle\Domain\Interfaces\StaticPuzzleInstanceProviderInterface;

class PuzzleInstanceService implements PuzzleInstanceServiceInterface
{

    /**
     * @var array<StaticPuzzleInstanceProviderInterface>
     */
    private array $staticInstanceProviders = [];

    public function __construct(
        private PuzzleTemplateRegistryInterface $puzzleTemplateRegistry,

        #[AutowireIterator('app.static_puzzle_provider')]
        iterable $staticInstanceProviders
    ) {
        foreach ($staticInstanceProviders as $provider) {
            $this->staticInstanceProviders[$provider->providesTemplateInstances()] = $provider;
        }
    }


    public function getInstanceByTemplateAndCode(string $templateSlug, string $instanceCode): ?PuzzleInstanceInterface
    {
        $template = $this->puzzleTemplateRegistry->getTemplate($templateSlug);
        if (!$template) {
            throw new TemplateNotFoundException(sprintf("No template found %s", $templateSlug));
        }
        if (isset($this->staticInstanceProviders[$templateSlug])) {
            die("HELLO WORLD");
        }
    }

    public function saveInstance(PuzzleInstanceInterface $instance): void
    {
        // TODO: Implement saveInstance() method.
    }

    public function deleteInstance(PuzzleInstanceInterface $instance): void
    {
        // TODO: Implement deleteInstance() method.
    }

    public function getStaticPuzzleInstancesForGame(Game $game): ArrayCollection
    {
        $staticPuzzles = new ArrayCollection();
        foreach ($this->staticPuzzleProviders as $provider) {
            $staticPuzzleInstances = $provider->getStaticPuzzleInstancesForGame($game);
            foreach ($staticPuzzleInstances as $staticPuzzleInstance) {
                $this->mapPuzzleTemplate($staticPuzzleInstance);
                $staticPuzzles->add($staticPuzzleInstance);
            }
        }
        return $staticPuzzles;
    }

    /**
     * @param StaticPuzzleInstanceInterface $puzzleInstance
     * @return void
     * @throws MismappedPuzzleTemplateException
     */
    private function mapPuzzleTemplate(StaticPuzzleInstanceInterface $puzzleInstance): void {

        $template = $this->puzzleTemplateRegistry->getTemplate($puzzleInstance->getTemplateSlug());
        if (null === $template) {
            throw new MismappedPuzzleTemplateException(sprintf("Could not find a template with slug %s", $puzzleInstance->getTemplateSlug()));
        }
        $puzzleInstance->setTemplate($template);
    }
}
