<?php

namespace App\Service\FileReader;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Model\BookData;
use Symfony\Component\Yaml\Yaml;

class YamlFileReader implements FileReaderInterface
{
    /**
     * @return BookData[]
     */
    public function read(UploadedFile $file): array
    {
        $books = [];

        $booksFileContents =  Yaml::parse(file_get_contents($file->getPathname()));

        foreach($booksFileContents as $bookData){
            $books[]  = new BookData($bookData['title'],  $bookData['author'], $bookData['description']);
        }

        return $books;
    }
}