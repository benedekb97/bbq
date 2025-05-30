<?php

declare(strict_types=1);

namespace App\Controller\AuthorisedUser;

use App\Entity\AuthorisedUser;
use App\Service\AuthorisedUserService;
use App\Slack\MessageFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class AddAuthorisedUserCommand extends AuthorisedUserCommand
{
    #[Route('slack/command/add-authorised-user')]
    public function __invoke(Request $request): JsonResponse
    {
        if (!$this->service->isUserAuthorised(
            $request->request->get('user_id'),
            $domain = $request->request->get('team_domain'),
        )) {
            return new JsonResponse([
                'blocks' => [
                    $this->messageFormatter->getHeader(':alert: You aren\'t allowed to do that.')
                ]
            ]);
        }

        $commandText = $request->request->get('text');

        $commandBits = explode(' ', $commandText);

        if (empty($commandBits)) {
            return new JsonResponse([
                'blocks' => [
                    $this->messageFormatter->getHeader('Please specify a user to authorise: /bbq-authorise-user @user')
                ]
            ]);
        }

        $userId = $commandBits[0];

        $matches = [];

        preg_match('/(U[A-Z0-9]{10})/i', $userId, $matches);

        $username = explode('>', explode('|', $userId)[1])[0];

        if (empty($matches)) {
            return new JsonResponse([
                'blocks' => [
                    $this->messageFormatter->getHeader(':doh: Could not extract user ID from message'),
                ]
            ]);
        }

        $user = $this->service->authoriseUser($matches[1], $domain, $username);

        return new JsonResponse([
            'blocks' => [
                $this->messageFormatter->getHeader(
                    sprintf('<@%s> Has been successfully authorised!', $user->username)
                )
            ]
        ]);
    }
}