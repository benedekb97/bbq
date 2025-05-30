<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\AuthorisedUser;
use App\Repository\AuthorisedUserRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class AuthorisedUserService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AuthorisedUserRepository $repository,
    ) {}

    public function authoriseUser(string $userId, string $domain, string $username): AuthorisedUser
    {
        $user = $this->repository->findOneBy([
            'userId' => $userId,
            'domain' => $domain,
            'username' => $username,
        ]);

        if ($user instanceof AuthorisedUser) {
            return $user;
        }

        $user = new AuthorisedUser();

        $user->userId = $userId;
        $user->domain = $domain;
        $user->username = $username;

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function isUserAuthorised(string $userId, string $domain): bool
    {
        return !empty($this->repository->findOneBy([
            'userId' => $userId,
            'domain' => $domain,
        ]));
    }

    public function deauthoriseUser(string $userId, string $domain): void
    {
        $user = $this->repository->findOneBy([
            'userId' => $userId,
            'domain' => $domain,
        ]);

        if ($user === null) {
            return;
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}