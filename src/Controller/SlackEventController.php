<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Queue;
use App\Enum\BBQCommand;
use App\Repository\QueueRepository;
use App\Service\BBQCommandService;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use ValueError;

class SlackEventController extends AbstractController
{
    public function __construct(
        private readonly BBQCommandService $service,
        private readonly QueueRepository $queueRepository,
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
        try {
            $command = $this->getCommand($request);

            return match ($command) {
                BBQCommand::JOIN => $this->service->joinQueue(
                    $this->getQueue($request),
                    $request->get('user_id')
                ),
                BBQCommand::LEAVE => $this->service->leaveQueue(
                    $this->getQueue($request),
                    $request->get('user_id')
                ),
                BBQCommand::LIST => $this->service->list(
                    $this->getQueue($request),
                ),
            };
        } catch (ValueError) {
            return $this->service->unrecognisedCommand($this->getCommandString($request));
        } catch (EntityNotFoundException $exception) {
            return $this->service->queueNotFound($exception->getMessage());
        }
    }

    private function getCommandString(Request $request): string
    {
        $requestText = $request->request->get('text');

        $commandParts = explode(' ', $requestText);

        return $commandParts[0];
    }

    private function getCommand(Request $request): BBQCommand
    {
        return BBQCommand::from($this->getCommandString($request));
    }

    private function getQueue(Request $request): Queue
    {
        $requestText = $request->request->get('text');

        $commandParts = explode(' ', $requestText);

        $queueName = $commandParts[1] ?? null;

        $queue = $queueName === null
            ? $this->queueRepository->findDefault($request->request->get('team_domain'))
            : $this->queueRepository->findOneBy([
                'name' => $queueName,
                'domain' => $request->request->get('team_domain'),
            ]);

        if ($queue === null) {
            throw new EntityNotFoundException($queueName);
        }

        return $queue;
    }
}