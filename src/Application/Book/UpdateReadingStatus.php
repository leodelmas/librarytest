<?php

namespace App\Application\Book;

use App\Domain\Book\Entity\UserBook;
use Doctrine\ORM\EntityManagerInterface;

class UpdateReadingStatus
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function execute(UserBook $userBook): UserBook
    {
        $userBook->setStatus($userBook->getStatus()->nextStatus());
        $this->em->flush();

        return $userBook;
    }
}
