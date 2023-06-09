<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Book;
use App\Model\FlashData;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Yaml\Yaml;

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

        switch($file->getClientMimeType()){
            case $allowedMimeTypes['csv']:

                if (($handle = fopen($file->getPathname(), "r")) !== FALSE) {
                    $counter= 0;
                    while (($data = fgetcsv($handle, null, ';')) !== FALSE) {
                        if($counter === 0){
                            $counter++;
                            continue;
                        }
                        
                        $book = (new Book())
                                ->setTitle($data[0])
                                ->setAuthor($data[1])
                                ->setDescription($data[2])
                        ;

                        $this->entityManager->persist($book);
                        $this->entityManager->flush();
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
                    $book = (new Book())
                        ->setTitle($book->title)
                        ->setAuthor($book->author)
                        ->setDescription($book->description)
                    ;

                    $this->entityManager->persist($book);
                    $this->entityManager->flush();
                    $counter++;
                 
                }

                $flashType = ($counter > 0) ? 'success' : 'danger';
                $flashMessage = 'Books imported: '.$counter;
    
            break;

            case  $allowedMimeTypes['yaml']:
                $books =  Yaml::parse($fileContents);

               $counter = 0;
               foreach($books as $bookData){
                    $bookObjectData =  (object) $bookData;
                    $book = (new Book())
                       ->setTitle($bookObjectData->title)
                       ->setAuthor($bookObjectData->author)
                       ->setDescription($bookObjectData->description)
                    ;

                   $this->entityManager->persist($book);
                   $this->entityManager->flush();
                   $counter++;
                
               }

                $flashType = ($counter > 0) ? 'success' : 'danger';
                $flashMessage = 'Books imported: '.$counter;
   
            break;            
        }      
        
        return new FlashData($flashType,  $flashMessage);
    }
}