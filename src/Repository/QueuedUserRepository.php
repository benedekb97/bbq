<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Queue;
use App\Entity\QueuedUser;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;

class QueuedUserRepository extends ServiceEntityRepository implements ObjectRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QueuedUser::class);
    }

    public function findUserInQueue(Queue $queue, string $userId): ?QueuedUser
    {
        return $this->createQueryBuilder('qu')
            ->where('qu.queue = :queue')
            ->andWhere('qu.userId = :userId')
            ->andWhere('qu.deletedAt is null')
            ->setParameter('queue', $queue)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function deleteAllExpired(): void
    {
        $this->createQueryBuilder('qu')
            ->where('qu.deletedAt is null')
            ->andWhere('qu.expiresAt < :now')
            ->setParameter('now', new DateTimeImmutable())
            ->update()
            ->set('qu.deletedAt', ':now')
            ->getQuery()
            ->execute();
    }
}