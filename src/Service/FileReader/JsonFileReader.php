<?php

namespace App\Service\FileReader;

use App\Model\BookData;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class JsonFileReader implements FileReaderInterface
{
    /**
     * @return BookData[]
     */
    public function read(UploadedFile $file): array
    {
        $books  = [];

        $fileContents = json_decode(file_get_contents($file->getPathname()));

        foreach($fileContents as $book){
            $books[] = new BookData($book->title, $book->author, $book->description);
        }
        return $books;
    }
}