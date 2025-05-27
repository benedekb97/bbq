<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class SlackEventController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {}

    #[Route('slack/event', methods: [Request::METHOD_POST])]
    public function handle(Request $request): JsonResponse
    {
        $this->logger->debug(implode(', ', $request->request->all()));

        if ($request->request->get('type') === 'url_verification') {
            $challenge = $request->request->get('challenge');

            return new JsonResponse([
                'challenge' => $challenge,
            ]);
        }

        return new JsonResponse();
    }
}