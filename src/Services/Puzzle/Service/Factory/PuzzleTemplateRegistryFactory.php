<?php

namespace App\Services\Puzzle\Service\Factory;

use App\Services\Puzzle\Domain\ConfigOptionDefinition;
use App\Services\Puzzle\Domain\PuzzleCredit;
use App\Services\Puzzle\Domain\PuzzleTemplate;
use App\Services\Puzzle\Service\Factory\Exceptions\InvalidConfigOptionDefinitionException;
use App\Services\Puzzle\Service\Factory\Exceptions\PuzzleTemplateRegistryBuildException;
use App\Services\Puzzle\Service\Interfaces\PuzzleTemplateRegistryInterface;
use App\Services\Puzzle\Service\PuzzleTemplateRegistry;
use Symfony\Component\Finder\Finder;

class PuzzleTemplateRegistryFactory
{
    public const string CONFIG_DIR = 'Config/TemplateDefinitions';

    private const string SLUG_REGEX = '/^[a-z0-9_]+$/';

    private const array TEMPLATE_FIELDS = [
        'title' => 'string',
        'created' => 'date',
        'slug' => 'slug',
        'description' => 'string',
        'category' => 'string',
        'type' => 'string',
        'tags' => 'array',
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
        'stringArray'
    ];

    /**
     * @throws PuzzleTemplateRegistryBuildException
     */
    public static function createPuzzleTemplateRegister(): PuzzleTemplateRegistryInterface {
        $finder = new Finder();
        $finder->files()->in(sprintf('%s/../%s', __DIR__, self::CONFIG_DIR))->name('*.json');
        if (!$finder->hasResults()) {
            throw new PuzzleTemplateRegistryBuildException("Could not build Template registry: No json files present");
        }
        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                $result = json_decode($file->getContents(), true );
                self::validateDefinition($result);
                $template = new PuzzleTemplate(
                    slug: $result['slug'],
                    title: $result['title'],
                    createdAt: new \DateTimeImmutable($result['created']),
                    description: $result['description'],
                    category: $result['category'],
                    authorEmail: $result['creator'],
                    credits: self::mapCredits($result['credits']),
                    configuration: self::mapConfigOptions($result['configOptions'])
                );
                dd($template);

            }
        }


        die('I got here');
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
    private static function validateDefinition(array $puzzleTemplateDefinitionArray): void {
        foreach ($puzzleTemplateDefinitionArray as $key => $definition) {
            if (!in_array($key, array_keys(self::TEMPLATE_FIELDS))) {
                throw new PuzzleTemplateRegistryBuildException(sprintf("Unknown field '%s' specified", $key));
            }
            foreach (self::TEMPLATE_FIELDS as $fieldName => $fieldType) {
                $value = $puzzleTemplateDefinitionArray[$fieldName];
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
                            $date = new \DateTime($value);
                        } catch (\DateMalformedStringException $e) {
                            throw new PuzzleTemplateRegistryBuildException(sprintf("%s must be a valid date", $fieldName));
                        }
                        break;

                    case 'configOptions':
                        self::validateConfigOptions($value);
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
            }
        }
    }
}
