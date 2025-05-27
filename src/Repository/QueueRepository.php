<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Queue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;

class QueueRepository extends ServiceEntityRepository implements ObjectRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Queue::class);
    }

    public function findDefault(): Queue
    {
        $queue = $this->createQueryBuilder('q')
            ->where('q.default = :default')
            ->orderBy('q.id', 'desc')
            ->setParameter('default', true)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($queue === null) {
            $queue = new Queue();

            $queue->name = 'deployment';
            $queue->default = true;

            $this->getEntityManager()->persist($queue);
            $this->getEntityManager()->flush();
        }

        return $queue;
    }
}