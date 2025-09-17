<?php

namespace App\Services\Puzzle\Service\Factory;

use App\Services\Puzzle\Service\Factory\Exceptions\PuzzleTemplateRegistryBuildException;
use App\Services\Puzzle\Service\Interfaces\PuzzleTemplateRegistryInterface;
use App\Services\Puzzle\Service\PuzzleTemplateRegistry;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Finder\Finder;

class PuzzleTemplateRegistryFactory
{
    public const string CONFIG_DIR = 'Config/TemplateDefinitions';

    public static function createPuzzleTemplateRegister(): PuzzleTemplateRegistryInterface {
        $finder = new Finder();
        $finder->files()->in(sprintf('%s/../%s', __DIR__, self::CONFIG_DIR))->name('*.yaml');
        if (!$finder->hasResults()) {
            throw new PuzzleTemplateRegistryBuildException("Could not build Template registry: No yaml files present");
        }
        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                $templateFile = Yaml::parseFile($file->getPathname());
                dd($templateFile);
                dd($file->getPathname());
            }
        }
        die('I got here');
    }
}
