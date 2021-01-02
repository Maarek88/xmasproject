<?php

namespace App\Service;

use App\Entity\Book;
use App\Repository\CategoryRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;

class BookService
{
    private $categoryRepository;
    private $tagRepository;
    private $entityManager;

    public function __construct(
        CategoryRepository $categoryRepository,
        TagRepository $tagRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->tagRepository      = $tagRepository;
        $this->entityManager      = $entityManager;
    }

    public function setBookCategory(Book $book)
    {
        $category = $this->categoryRepository->findOneBy(['name' => $book->getCategory()->getName()]);

        if ($category) {
            $book->setCategory($category);
        }
    }

    public function setBookTags(Book $book)
    {
        foreach ($book->getTags() as $newTag) {
            $tag = $this->tagRepository->findOneBy(['name' => $newTag->getName()]);
            if ($tag) {
                $book->removeTag($newTag);
                $book->addTag($tag);
            } else {
                $this->entityManager->persist($newTag);
            }
        }
    }
}
