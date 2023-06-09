<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Book;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\FileReader\FileReader;

class BooksImporterService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FileReader $fileReader
    )
    {
        
    }

    public function importFromFile(UploadedFile $file, array $allowedMimeTypes): int
    {

        $booksDataArray = $this->fileReader->readFromFile($file, $allowedMimeTypes);

        $counter = 0;
        foreach($booksDataArray as $book){

            $book = (new Book())
                    ->setTitle($book->getTitle())
                    ->setAuthor($book->getAuthor())
                    ->setDescription($book->getDescription())
            ;

            $this->entityManager->persist($book);
            $this->entityManager->flush();

            $counter++;
        }
        
        return $counter;
    }
}