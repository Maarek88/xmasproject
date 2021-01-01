<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Service\BookService;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class BookController extends AbstractFOSRestController
{
    private $bookRepository;
    private $entityManager;
    private $bookService;

    public function __construct(
        BookRepository $bookRepository,
        EntityManagerInterface $entityManager,
        BookService $bookService
    ) {
        $this->bookRepository = $bookRepository;
        $this->entityManager  = $entityManager;
        $this->bookService    = $bookService;
    }

    /**
     * @Rest\Post("/api/book")
     *
     * @param Request $request
     * @return View
     */
    public function addBook(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(BookType::class, $book = new Book());

        if ($form->submit($data)->isSubmitted() && !$form->isValid()) {
            return $this->view($form->getErrors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->bookService->setBookCategory($book);
        $this->bookService->setBookTags($book);

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $this->view($book, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Get("/api/book")
     * @return View
     */
    public function getAllBooks()
    {
        $books = $this->bookRepository->findAllNotDeleted();

        return $this->view($books, Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/api/book/{uuid}")
     * @param string $uuid
     * @return View
     */
    public function getOneBook(string $uuid)
    {
        $book = $this->bookRepository->findOneByUuid($uuid);

        if (!$book) {
            throw new NotFoundHttpException('Book not found');
        }

        return $this->view($book, Response::HTTP_OK);
    }

    /**
     * @Rest\Put("/api/book/{uuid}")
     * @param string $uuid
     * @param Request $request
     * @return View
     */
    public function updateBook(string $uuid, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $book = $this->bookRepository->findOneByUuid($uuid);

        if (!$book) {
            return $this->view(null, Response::HTTP_NO_CONTENT);
        }

        $form = $this->createForm(BookType::class, $newBook = new Book());

        if ($form->submit($data)->isSubmitted() && !$form->isValid()) {
            return $this->view($form->getErrors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->bookService->setBookCategory($newBook);
        $this->bookService->setBookTags($newBook);

        $book->setTitle($newBook->getTitle());
        $book->setSummary($newBook->getSummary());
        $book->setReleaseDate($newBook->getReleaseDate());
        $book->setCategory($newBook->getCategory());
        $book->setTags($newBook->getTags());

        $this->entityManager->flush();

        return $this->view($book, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete("/api/book/{uuid}")
     * @param string $uuid
     * @return View
     */
    public function deleteBook(string $uuid)
    {
        $book = $this->bookRepository->findOneBy(['uuid' => $uuid]);

        if (!$book) {
            throw new NotFoundHttpException('Book not found');
        }

        $this->entityManager->remove($book);
        $this->entityManager->flush();

        return $this->view(null, Response::HTTP_OK);
    }
}