<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BooksUploaderType;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BooksController extends AbstractController
{
    public function __construct(
       private  EntityManagerInterface $entityManager,
       private  BookRepository $bookRepository
    )
    {
        
    }

    #[Route('/', name: 'app_home')]
    public function homepage()
    {
        return $this->redirectToRoute('app_books_list');
    }
    
    #[Route('/index', name: 'app_books_index')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(BookType::class, new Book());

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->entityManager->persist($form->getData());
            $this->entityManager->flush();

            $this->addFlash(
                'success',
                'Your changes were saved!'
            );

            return $this->redirectToRoute('app_books_index');
        }

        return $this->render('book/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/list', name: 'app_books_list')]
    public function list()
    {
        return $this->render('book/list.html.twig', [
            'books' => $this->bookRepository->findAll()
        ]);
    }

    #[Route('/upload', name:'app_books_upload')]
    public function upload(Request $request)
    {
        $allowedMimeTypes = BooksUploaderType::ALLOWED_MIME_TYPES;

        $form = $this->createForm(BooksUploaderType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $file = $form['attachment']->getData();

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
            $this->addFlash(
                $flashType,
                $flashMessage
            );
            return $this->redirectToRoute('app_books_upload');
        }

        return $this->render('book/upload.html.twig', [
            'form' => $form
        ]);
    }
}
