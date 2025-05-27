<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class SlackEventController extends AbstractController
{
    #[Route('slack/event', methods: [Request::METHOD_POST])]
    public function handle(Request $request): JsonResponse
    {
        if ($request->get('type') === 'url_verification') {
            $challenge = $request->get('challenge');

            return new JsonResponse([
                'challenge' => $challenge,
            ]);
        }

        return new JsonResponse();
    }
}