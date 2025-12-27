<?php

declare(strict_types=1);

namespace App\Controller\Traits;

use http\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

trait HandlesImageUploadsTrait
{

    protected function handleImageUpload(
        UploadedFile $imageFile,
        string $destination
    ): string {

        if ($this->slugger === null) {
            throw new RuntimeException('You must set a valid slugger');
        }
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);

        $imageFileName = strtolower(sprintf("%s-%s.%s", $safeFilename, uniqid(), $imageFile->guessExtension()));


        try {
            $imageFile->move($destination, $imageFileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return sprintf("%s/%s", $this->stripPublic($destination), $imageFileName);
    }

    protected function stripPublic(string $path): string {
        return str_replace("/var/www/public", "", $path);
    }
}
