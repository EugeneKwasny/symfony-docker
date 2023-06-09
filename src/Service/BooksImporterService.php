<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Book;
use App\Model\FlashData;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Yaml\Yaml;
use App\Model\BookData;

class BooksImporterService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
        
    }

    public function importFromFile(UploadedFile $file, array $allowedMimeTypes): FlashData
    {

        $fileContents  = file_get_contents($file->getPathname());

        $flashType = 'danger';
        $flashMessage = 'Unknown file extension. Allowed file types: '.implode(', ', $allowedMimeTypes);


        $books = new ArrayCollection();

        switch($file->getClientMimeType()){
            case $allowedMimeTypes['csv']:

                if (($handle = fopen($file->getPathname(), "r")) !== FALSE) {
                    $counter= 0;
                    while (($data = fgetcsv($handle, null, ';')) !== FALSE) {
                        if($counter === 0){
                            $counter++;
                            continue;
                        }
                        $this->saveToDb(new BookData(
                            $data[0], 
                            $data[1], 
                            $data[2]
                        ));

                        $counter++;
                    }
                    fclose($handle);

                    $flashType = ($counter> 0) ? 'success' : 'danger';
                    $flashMessage = 'Books imported: '.--$counter;
        
                }

            break;
            case $allowedMimeTypes['json']:
                $books = json_decode($fileContents);
    
                $counter = 0;
                foreach($books as $book){
                    $this->saveToDb(new BookData(
                        $book->title, 
                        $book->author, 
                        $book->description
                    ));
                    $counter++;
                }

                $flashType = ($counter > 0) ? 'success' : 'danger';
                $flashMessage = 'Books imported: '.$counter;
    
            break;

            case  $allowedMimeTypes['yaml']:
                $books =  Yaml::parse($fileContents);

                $counter = 0;
                foreach($books as $bookData){
                    $this->saveToDb
                        (new BookData(
                            $bookData['title'], 
                            $bookData['author'], 
                            $bookData['description']
                    ));
                    $counter++;  
                }

                $flashType = ($counter > 0) ? 'success' : 'danger';
                $flashMessage = 'Books imported: '.$counter;
   
            break;            
        }   
        
        return new FlashData($flashType,  $flashMessage);
    }


    private function saveToDb(BookData $bookData): void
    {
        $book = (new Book())
                    ->setTitle($bookData->getTitle())
                    ->setAuthor($bookData->getAuthor())
                    ->setDescription($bookData->getDescription())
        ;

        $this->entityManager->persist($book);
        $this->entityManager->flush();
    }
}