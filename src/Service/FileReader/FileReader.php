<?php

namespace App\Service\FileReader;

use App\Exception\CommonException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Model\BookData;
use App\Service\FileReader\Types\TypeInterface;
use Symfony\Component\HttpFoundation\Response;

class FileReader
{
    /**
     * @param UploadedFile $file
     * @param TypeInterface[] $fileReaderExtensions
     * 
     * @return BookData[]
     */
    public function readFromFile(UploadedFile $file, array $fileReaderExtensions): array
    {   
        $extensionType = array_key_exists($file->getClientMimeType(), $fileReaderExtensions);

        if(!$extensionType){
            throw new CommonException('Unknown MIME type. Allowed file extensions: '.implode(', ', array_keys($fileReaderExtensions)), Response::HTTP_OK);
        }

        return $this->read($file, new $fileReaderExtensions[$file->getClientMimeType()]);

    }
    /**
     * @return BookData[]
     */
    public function read(UploadedFile $file, TypeInterface $fileReader): array
    {
        return $fileReader->read($file);
    }
}