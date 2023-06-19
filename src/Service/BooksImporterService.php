<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Book;
use App\Form\BooksUploaderType;
use App\Service\FileReader\CsvFileReader;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\FileReader\FileReader;
use App\Service\FileReader\JsonFileReader;
use App\Service\FileReader\YamlFileReader;

class BooksImporterService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FileReader $fileReader
    )
    {
        
    }

    public function importFromFile(UploadedFile $file): int
    {

        $booksDataArray = $this->fileReader->readFromFile($file, BooksUploaderType::ALLOWED_MIME_TYPES);

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