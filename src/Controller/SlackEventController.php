<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\QueuedUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class SlackEventController extends AbstractController
{
    public function __construct(
        private QueuedUserRepository $repository,
    ) {}

    #[Route('slack/event', methods: [Request::METHOD_POST])]
    public function handle(Request $request): JsonResponse
    {
        if ($request->request->get('type') === 'url_verification') {
            $challenge = $request->request->get('challenge');

            return new JsonResponse([
                'challenge' => $challenge,
            ]);
        }

        return new JsonResponse();
    }

    #[Route('slack/command', methods: [Request::METHOD_POST, Request::METHOD_GET])]
    public function command(Request $request): JsonResponse
    {
        $this->repository->deleteAllExpired();

        return new JsonResponse(
            [
                'blocks' => [
                    [
                        'type' => 'section',
                        'text' => [
                            'type' => 'mrkdwn',
                            'text' => '*So long and thanks for all the fish*',
                        ]
                    ]
                ]
            ]
        );
    }
}