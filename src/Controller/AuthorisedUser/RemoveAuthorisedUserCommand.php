<?php

declare(strict_types=1);

namespace App\Controller\AuthorisedUser;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class RemoveAuthorisedUserCommand extends AuthorisedUserCommand
{
    #[Route('slack/command/remove-authorised-user')]
    public function __invoke(Request $request): JsonResponse
    {
        if (!$this->service->isUserAuthorised(
            $currentUser = $request->request->get('user_id'),
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

        preg_match('/(U[A-Z0-9]{10})/i', $userId, $matches);

        $username = explode('>', explode('|', $userId)[1])[0];

        if (empty($matches)) {
            return new JsonResponse([
                'blocks' => [
                    $this->messageFormatter->getHeader(':doh: Could not extract user ID from message'),
                ]
            ]);
        }

        if ($matches[1] === $currentUser) {
            return new JsonResponse([
                'blocks' => [
                    $this->messageFormatter->getHeader(':doh: You can\'t remove yourself'),
                ]
            ]);
        }

        $this->service->deauthoriseUser($matches[1], $domain);

        return new JsonResponse([
            'blocks' => [
                $this->messageFormatter->getHeader(
                    sprintf('<@%s> Has been removed!', $username)
                )
            ]
        ]);
    }
}