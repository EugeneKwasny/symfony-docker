<?php

namespace App\Controller;

use App\Entity\Book;
use App\Exception\CommonException;
use App\Form\BooksUploaderType;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Service\BooksImporterService;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BooksController extends AbstractController
{
    public function __construct(
       private  EntityManagerInterface $entityManager,
       private  BookRepository $bookRepository,
       private BooksImporterService $booksImporterService
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

    #[Route('/list/{page<\d+>}', name: 'app_books_list')]
    public function list(int $page = 1)
    {
        $queryBuilder = $this->bookRepository->createfindAllQuueryBuilder();

        $pager = new Pagerfanta(new QueryAdapter($queryBuilder));
        $pager->setMaxPerPage(10);
        $pager->setCurrentPage($page);

        return $this->render('book/list.html.twig', [
            'pager' => $pager,
        ]);
    }

    #[Route('/upload', name:'app_books_upload')]
    public function upload(Request $request)
    {

        try{
            $form = $this->createForm(BooksUploaderType::class);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid())
            {
                $booksImported = $this->booksImporterService->importFromFile($form['attachment']->getData());

                $this->addFlash(
                    ($booksImported > 0) ? 'success' : 'danger', 
                    'Books imported: '.$booksImported
                );

                return $this->redirectToRoute('app_books_upload');
            }

            return $this->render('book/upload.html.twig', [
                'form' => $form
            ]);

        }catch(CommonException $exception){
            $this->addFlash(
                'danger',
                $exception->getMessage()
            );
            return $this->redirectToRoute('app_books_upload');
        }
    }
}
