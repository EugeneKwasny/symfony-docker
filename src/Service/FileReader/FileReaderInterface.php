<?php

namespace App\Service\FileReader;

use App\Model\BookData;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FileReaderInterface
{
    /**
     * @return BookData[]
     */
    public function read(UploadedFile $file): array;
}