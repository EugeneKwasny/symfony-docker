<?php

namespace App\Service\FileReader\Types;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Model\BookData;
use Symfony\Component\Yaml\Yaml as YamlParser;

class Yaml implements TypeInterface
{
    /**
     * @return BookData[]
     */
    public function read(UploadedFile $file): array
    {
        $books = [];

        $booksFileContents =  YamlParser::parse(file_get_contents($file->getPathname()));

        foreach($booksFileContents as $bookData){
            $books[]  = new BookData($bookData['title'],  $bookData['author'], $bookData['description']);
        }

        return $books;
    }
}