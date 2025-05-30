<?php

declare(strict_types=1);

namespace App\Controller\AuthorisedUser;

use App\Service\AuthorisedUserService;
use App\Slack\MessageFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AuthorisedUserCommand extends AbstractController
{
    public function __construct(
        protected readonly AuthorisedUserService $service,
        protected readonly MessageFormatter $messageFormatter,
    ) {}
}