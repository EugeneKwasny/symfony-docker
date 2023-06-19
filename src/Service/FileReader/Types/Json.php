<?php

namespace App\Service\FileReader\Types;

use App\Model\BookData;
use App\Service\FileReader\Types\TypeInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Json implements TypeInterface
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