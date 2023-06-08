<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
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
}
