<?php

namespace App\Application\Book;

use App\Domain\Book\Entity\Book;
use App\Domain\Book\Entity\UserBook;
use App\Domain\Book\Port\BookCatalogInterface;
use App\Domain\Book\Port\CoverStorageInterface;
use App\Domain\User\Entity\User;
use App\Infrastructure\Persistence\BookRepository;
use App\Infrastructure\Persistence\UserBookRepository;
use Doctrine\ORM\EntityManagerInterface;

class AddBookToCollection
{
    public function __construct(
        private BookCatalogInterface $catalog,
        private CoverStorageInterface $coverStorage,
        private BookRepository $bookRepository,
        private UserBookRepository $userBookRepository,
        private EntityManagerInterface $em,
    ) {
    }

    public function execute(
        User $user,
        string $key,
        string $title,
        ?string $author,
        ?int $firstPublishYear,
        ?string $sourceCoverUrl,
    ): UserBook {
        $book = $this->bookRepository->findOneBy(['openLibraryKey' => $key]);

        if ($book && $this->userBookRepository->findOneBy(['user' => $user, 'book' => $book])) {
            throw new \DomainException('Book already in collection');
        }

        if (!$book) {
            $book = new Book();
            $book->setTitle($title);
            $book->setAuthor($author);
            $book->setFirstPublishYear($firstPublishYear);
            $book->setOpenLibraryKey($key);
            $book->setCoverUrl($sourceCoverUrl ? $this->coverStorage->store($key, $sourceCoverUrl) : null);
            $book->setDescription($this->catalog->getWorkDescription($key));
            $this->em->persist($book);
        }

        $userBook = new UserBook();
        $userBook->setUser($user);
        $userBook->setBook($book);
        $this->em->persist($userBook);
        $this->em->flush();

        return $userBook;
    }
}
