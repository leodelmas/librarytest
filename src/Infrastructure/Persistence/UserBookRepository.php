<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Book\Entity\UserBook;
use App\Domain\User\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserBook>
 */
class UserBookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserBook::class);
    }

    /**
     * @return UserBook[]
     */
    public function findByUserOrderedByStatus(User $user): array
    {
        return $this->createQueryBuilder('ub')
            ->where('ub.user = :user')
            ->setParameter('user', $user)
            ->addSelect("CASE
                WHEN ub.status = 'reading' THEN 0
                WHEN ub.status = 'to_read' THEN 1
                WHEN ub.status = 'read' THEN 2
                ELSE 3
            END AS HIDDEN statusOrder")
            ->orderBy('statusOrder')
            ->getQuery()
            ->getResult();
    }
}
