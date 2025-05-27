<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AuthorisedUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;

class AuthorisedUserRepository extends ServiceEntityRepository implements ObjectRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuthorisedUser::class);
    }
}