<?php

namespace App\Service\FileReader;

use App\Exception\CommonException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Model\BookData;
use Symfony\Component\HttpFoundation\Response;

class FileReader
{
    /**
     * @return BookData[]
     */
    public function readFromFile(UploadedFile $file, array $allowedMimeTypes): array
    {   
        switch($file->getClientMimeType()){
            case $allowedMimeTypes['csv']:
                return $this->read($file, new CsvFileReader());
            case $allowedMimeTypes['json']:
                return $this->read($file, new JsonFileReader());
            case  $allowedMimeTypes['yaml']:
                return $this->read($file, new YamlFileReader());
            default:
                throw new CommonException('Unknown MIME type. Allowed file extensions: '.implode(', ', array_keys($allowedMimeTypes)), Response::HTTP_OK);
        }  
    }
    /**
     * @return BookData[]
     */
    public function read(UploadedFile $file, FileReaderInterface $fileReader): array
    {
        return $fileReader->read($file);
    }
}