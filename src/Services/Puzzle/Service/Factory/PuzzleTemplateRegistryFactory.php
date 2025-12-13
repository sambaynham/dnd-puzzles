<?php

declare(strict_types=1);

namespace App\Services\Puzzle\Service\Factory;

use App\Services\Puzzle\Domain\ConfigOptionDefinition;
use App\Services\Puzzle\Domain\PuzzleCategory;
use App\Services\Puzzle\Domain\PuzzleCredit;
use App\Services\Puzzle\Domain\PuzzleTemplate;
use App\Services\Puzzle\Infrastructure\PuzzleCategoryRepository;
use App\Services\Puzzle\Service\Factory\Exceptions\InvalidConfigOptionDefinitionException;
use App\Services\Puzzle\Service\Factory\Exceptions\PuzzleTemplateRegistryBuildException;
use App\Services\Puzzle\Service\Interfaces\PuzzleTemplateRegistryInterface;
use App\Services\Puzzle\Service\PuzzleTemplateRegistry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

#[AutoconfigureTag('kernel.cache_warmer')]
class PuzzleTemplateRegistryFactory implements CacheWarmerInterface
{
    private const int DEFAULT_CACHE_TTL = 31556952;

    private const string CACHE_KEY = 'puzzleTemplateRegistry';

    public const string CONFIG_DIR = 'Config/TemplateDefinitions';

    private const string SLUG_REGEX = '/^[a-z0-9_]+$/';

    private const array TEMPLATE_FIELDS = [
        'title' => 'string',
        'created' => 'date',
        'slug' => 'slug',
        'description' => 'string',
        'categories' => 'array',
        'type' => 'string',
        'static' => 'boolean',
        'staticCreateRoute' => 'route',
        'staticEditRoute' => 'route',
        'creator' => 'email',
        'credits' => 'array',
        'configOptions' => 'configOptions'
    ];
    private const array CONFIG_OPTION_FIELDS = [
        'configName' => 'configName',
        'label' => 'string',
        'type' => 'type'
    ];

    private const array KNOWN_CONFIG_OPTION_TYPES = [
        'text',
        'stringArray',
        'dieRoll',
        'route'
    ];

    public function __construct(
        private readonly CacheInterface $cache,
        private readonly PuzzleCategoryRepository $puzzleCategoryRepository,
        private EntityManagerInterface $entityManager,
        private RouterInterface $router
    ) {
    }
    /**
     * @throws PuzzleTemplateRegistryBuildException|\DateMalformedStringException
     * @throws InvalidArgumentException
     */
    public function createPuzzleTemplateRegistry(bool $forceRefresh = false): PuzzleTemplateRegistryInterface {
        if ($forceRefresh) {
            $this->cache->delete(self::CACHE_KEY);
        }
        return $this->buildRegistry();
    }

    private function buildRegistry(): PuzzleTemplateRegistry {
        return $this->cache->get(self::CACHE_KEY, function (ItemInterface $item) {
            $item->expiresAfter(self::DEFAULT_CACHE_TTL);
            $registryContent = [];
            $finder = new Finder();
            $finder->files()->in(sprintf('%s/../%s', __DIR__, self::CONFIG_DIR))->name('*.json');
            if (!$finder->hasResults()) {
                throw new PuzzleTemplateRegistryBuildException("Could not build Template registry: No json files present");
            }
            if ($finder->hasResults()) {
                foreach ($finder as $file) {
                    $result = json_decode($file->getContents(), true );
                    $this->validateDefinition($result);
                    $template = new PuzzleTemplate(
                        slug: $result['slug'],
                        title: $result['title'],
                        createdAt: new \DateTimeImmutable($result['created']),
                        description: $result['description'],
                        categories: $this->mapCategories($result['categories']),
                        authorEmail: $result['creator'],
                        static: $result['static'],
                        credits: self::mapCredits($result['credits']),
                        configuration: self::mapConfigOptions($result['configOptions']),
                        staticCreateRoute: $result['staticCreateRoute'] ?? null,
                        staticEditRoute: $result['staticEditRoute'] ?? null
                    );

                    $registryContent[$template->getSlug()] = $template;


                }
            }
            return new PuzzleTemplateRegistry(templates: $registryContent);
        });
    }

    public static function mapCredits(array $credits): array {
        $creditArray = [];
        foreach ($credits as $credit) {
            $creditArray[] = new PuzzleCredit(
                name: $credit['name'],
                created: $credit['created'],
                email: $credit['email'] ?? null
            );
        }
        return $creditArray;
    }

    public static function mapConfigOptions(array $configOptionDefinitions): array {
        $configOptions = [];
        foreach ($configOptionDefinitions as $configOptionDefinition) {
            $configOptions[] = new ConfigOptionDefinition(
                configName: $configOptionDefinition['configName'],
                label: $configOptionDefinition['label'],
                type: $configOptionDefinition['type'],
                helpText: $configOptionDefinition['helpText'] ?? null
            );
        }

        return $configOptions;

    }

    /**
     * @throws PuzzleTemplateRegistryBuildException
     */
    private function validateDefinition(array $puzzleTemplateDefinitionArray): void {
        foreach ($puzzleTemplateDefinitionArray as $key => $definition) {
            if (!in_array($key, array_keys(self::TEMPLATE_FIELDS))) {
                throw new PuzzleTemplateRegistryBuildException(sprintf("Unknown field '%s' specified", $key));
            }
            foreach (self::TEMPLATE_FIELDS as $fieldName => $fieldType) {
                $value = $puzzleTemplateDefinitionArray[$fieldName] ?? null;
                switch ($fieldType) {
                    case 'string':
                        if (!is_string($value)) {
                            throw new PuzzleTemplateRegistryBuildException(sprintf("%s must be a string", $fieldName));
                        }
                        break;

                    case 'slug':
                        if (!preg_match(self::SLUG_REGEX, $value)) {
                            throw new PuzzleTemplateRegistryBuildException("Slugs may only contain lowercase letters, numbers and underscores.");
                        }
                        break;
                    case 'array':
                        if (!is_array($value)) {
                            throw new PuzzleTemplateRegistryBuildException(sprintf("%s must be an array", $fieldName));
                        }
                        break;
                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            throw new PuzzleTemplateRegistryBuildException(sprintf("%s must be a valid email address", $fieldName));
                        }
                        break;
                    case 'date':
                        try {
                            new \DateTime($value);
                        } catch (\DateMalformedStringException $e) {
                            throw new PuzzleTemplateRegistryBuildException(sprintf("%s must be a valid date", $fieldName));
                        }
                        break;
                    case 'boolean':
                        if (!is_bool($value)) {
                            throw new PuzzleTemplateRegistryBuildException(sprintf("%s must be an boolean", $fieldName));
                        }
                        break;
                    case 'configOptions':
                        self::validateConfigOptions($value);
                        break;

                    case 'route':
                        if ($value === null) {
                            if ($puzzleTemplateDefinitionArray['static'] === true) {
                                throw new PuzzleTemplateRegistryBuildException(sprintf("The template %s is static, but no static configuration route has been provided", $puzzleTemplateDefinitionArray['title']));
                            } else {
                                break;
                            }
                        }
                        try {
                            $this->router->generate($value, [
                                'gameSlug' => 'test',
                                'templateSlug' => 'test',
                                'instanceCode' => 'test'
                            ]);
                        } catch (RouteNotFoundException $e) {
                            throw new PuzzleTemplateRegistryBuildException(sprintf("The specified static configuration route, %s, could not be found", $value));
                        }

                        break;
                    default:
                        throw new PuzzleTemplateRegistryBuildException(sprintf("Unknown field type %s specified", $fieldType));
                }
            }
        }
    }



    /**
     * @param array $configOptionDefinitions
     * @return void
     * @throws InvalidConfigOptionDefinitionException
     */
    public static function validateConfigOptions(array $configOptionDefinitions): void {
        foreach ($configOptionDefinitions as $configOptionDefinition) {

            foreach (self::CONFIG_OPTION_FIELDS as $fieldName => $type) {
                if (!isset($configOptionDefinition[$fieldName])) {
                    throw new InvalidConfigOptionDefinitionException(sprintf("Invalid config option. The subfield '%s' is required", $fieldName));
                }

                if (!in_array($configOptionDefinition['type'], self::KNOWN_CONFIG_OPTION_TYPES)) {
                    throw new InvalidConfigOptionDefinitionException(sprintf("Invalid Config option. The type '%s' is not known", $configOptionDefinition['type']));
                }

                switch ($type) {
                    case 'configName':
                        if (!preg_match(self::SLUG_REGEX, $configOptionDefinition['configName'])) {
                            throw new InvalidConfigOptionDefinitionException("Slugs may only contain lowercase letters, numbers and underscores.");
                        }
                        break;
                    case 'string':
                        if (!is_string($configOptionDefinition[$fieldName])) {
                            throw new InvalidConfigOptionDefinitionException(sprintf("%s must be a string", $fieldName));
                        }
                        break;
                    case 'type':
                        if (!in_array($configOptionDefinition[$fieldName], self::KNOWN_CONFIG_OPTION_TYPES)) {
                            throw new InvalidConfigOptionDefinitionException(sprintf("Invalid Config option. The type '%s' is not known", $configOptionDefinition[$fieldName]));
                        }
                        break;
                    case 'boolean':
                        if (!is_bool($configOptionDefinition[$fieldName])) {
                            throw new InvalidConfigOptionDefinitionException(sprintf("%s must be a boolean", $fieldName));
                        }
                        break;
                    default:
                        throw new InvalidConfigOptionDefinitionException(sprintf("Unknown field type %s specified", $type));
                }
            }
        }
    }

    public function isOptional(): bool
    {
        return false;
    }

    public function warmUp(string $cacheDir, ?string $buildDir = null): array
    {
        $this->cache->delete(self::CACHE_KEY);
        $this->buildRegistry();
        return [];
    }

    public function mapCategories(array $categories): ArrayCollection {
        $categoriesCollection = new ArrayCollection();
        foreach ($categories as $categorySlug) {
            $category = $this->puzzleCategoryRepository->findByHandle($categorySlug);
            if (null === $category) {
                $label = self::generateCategoryLabel($categorySlug);
                $category = new PuzzleCategory(
                    handle: $categorySlug,
                    label: $label,
                    description: $label
                );
                $this->entityManager->persist($category);

            }
            $categoriesCollection->add($category);
        }
        $this->entityManager->flush();
        return $categoriesCollection;
    }

    private static function generateCategoryLabel(string $categorySlug): string {

        $categorySlug = str_replace(search:'_', replace: ' ', subject: $categorySlug);
        $categorySlug = ucwords($categorySlug);
        $categorySlug = str_replace('And', 'and', $categorySlug);
        return $categorySlug;
    }
}
