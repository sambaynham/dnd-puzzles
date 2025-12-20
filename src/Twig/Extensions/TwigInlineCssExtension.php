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
    /*
return new TwigFunction(
'inlineCss',
    function (string $path, bool $forceInlining = false, string $media = 'screen') {

        $cacheKey = $this->pathToCacheKey('css', $path);

        $cachedValue = Cache::get($cacheKey);
        $testMode = config('twig-magic.test-mode');
        $debug = config('twig-magic.debug', false);
        if ($cachedValue && ! $testMode) {
            return $cachedValue;
        }
        $absolutePath = public_path($path);

        if (file_exists($absolutePath)) {
            $extension = File::extension($absolutePath);
            if ($extension == 'css') {
                $cutOffSize = config('twig-magic.css_inline_cutoff');

                if (File::size($absolutePath) <= $cutOffSize or $forceInlining) {
                    $payload = $debug ? "<!-- {$path} Inlined by TwigMagic -->" : "";
                    $payload .= sprintf('<style>%s</style>', File::get($absolutePath));

                    $payload .= $debug ? "<!-- End {$path} Inlining -->" : "";
                } else {
                    $payload = $debug ? "<!-- {$path} Too big for TwigMagic to inline -->" : "";
                    $payload .= sprintf("<link rel='stylesheet' media='%s' href='/%s'>", $media, $path);

                }
                Cache::set($cacheKey, $payload);
                return $payload;
            }
        } else {
            throw new Exception("{$path} not found");
        }
        return null;
    },
[
'is_safe' => [
'html'
]
]
);*/
}
