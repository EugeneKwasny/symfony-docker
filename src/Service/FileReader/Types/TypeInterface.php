<?php

namespace App\Service\FileReader\Types;

use App\Model\BookData;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface TypeInterface
{
    /**
     * @return BookData[]
     */
    public function read(UploadedFile $file): array;
}