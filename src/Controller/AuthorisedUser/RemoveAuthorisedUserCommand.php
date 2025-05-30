<?php

declare(strict_types=1);

namespace App\Controller\AuthorisedUser;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class RemoveAuthorisedUserCommand extends AuthorisedUserCommand
{
    #[Route('/slack/command/remove-authorised-user')]
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
                    $this->messageFormatter->getHeader('Please specify a user to remove: /bbq-remove-user @user')
                ]
            ]);
        }

        $userId = $commandBits[0];

        $matches = [];
        $usernameMatches = [];

        preg_match('/(U[A-Z0-9]{10})/i', $userId, $matches);

        preg_match('/\|([A-Za-z0-9]+)>/', $userId, $usernameMatches);

        if (empty($matches)) {
            return new JsonResponse([
                'blocks' => [
                    $this->messageFormatter->getHeader(':doh: Could not extract user ID from message'),
                ]
            ]);
        }

        $this->service->deauthoriseUser($matches[1], $domain);

        return new JsonResponse([
            'blocks' => [
                $this->messageFormatter->getHeader(
                    sprintf('<@%s> Has been removed!', $usernameMatches[1])
                )
            ]
        ]);
    }
}