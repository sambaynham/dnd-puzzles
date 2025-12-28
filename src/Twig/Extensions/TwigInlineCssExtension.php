<?php

namespace App\Twig\Extensions;


use Symfony\Component\DependencyInjection\Attribute\Autowire;

use Twig\Attribute\AsTwigFunction;

class TwigInlineCssExtension
{
    private const string PUBLIC_DIR = 'public';

    public function __construct(
        #[Autowire(param: 'kernel.project_dir')]
        private string $projectDir
    ) {}


    #[AsTwigFunction('inline_css')]
    public function inlineCss(string $path): string {
        $basePath =realpath($this->projectDir . DIRECTORY_SEPARATOR . self::PUBLIC_DIR);
        try {
            $file = file_get_contents($basePath . $path);
        } catch (\Exception $e) {
            return '';
        }

        return $file;
    }

}
