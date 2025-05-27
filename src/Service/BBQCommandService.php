<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Queue;
use App\Entity\QueuedUser;
use App\Repository\QueuedUserRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

readonly class BBQCommandService
{
    public function __construct(
        private QueuedUserRepository $queuedUserRepository,
        private EntityManagerInterface $entityManager,
    ) {}

    public function joinQueue(Queue $queue, string $userId): JsonResponse
    {
        $user = $this->queuedUserRepository->findUserInQueue($queue, $userId);

        if ($user !== null) {
            return new JsonResponse([
                'blocks' => [
                    $this->getHeader(':doh: You are already in the '.$queue->name.' queue!')
                ]
            ]);
        }

        $user = new QueuedUser();

        $user->queue = $queue;
        $user->userId = $userId;
        $user->expiresAt = $queue->expiryInMinutes !== null
            ? (new DateTime())->add(DateInterval::createFromDateString(sprintf('%d minute', $queue->expiryInMinutes)))
            : null;

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse([
            'blocks' => [
                $this->getHeader('Queue joined successfully!'),
                $this->getSection(
                    '*Queue*\n'.$queue->name,
                    '*Expiry length*\n'.($queue->expiryInMinutes ?? ':dinkdonk:').' minutes'
                ),
            ]
        ]);
    }

    public function leaveQueue(Queue $queue, string $userId): JsonResponse
    {
        $user = $this->queuedUserRepository->findUserInQueue($queue, $userId);

        if ($user === null) {
            return new JsonResponse([
                'blocks' => [
                    $this->getHeader(':doh: You never joined the ' . $queue->name . ' queue!')
                ]
            ]);
        }

        $user->deletedAt = new DateTime();

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse([
            'blocks' => [
                $this->getHeader('You\'ve successfully left the ' . $queue->name . ' queue!'),
            ]
        ]);
    }

    public function unrecognisedCommand(string $text): JsonResponse
    {
        return new JsonResponse([
            'blocks' => [
                $this->getHeader(':doh: Unrecognised command \''.$text.'\'.'),
            ],
        ]);
    }

    private function getHeader(string $text): array
    {
        return [
            'type' => 'header',
            'text' => [
                'type' => 'plain_text',
                'text' => $text,
                'emoji' => true,
            ]
        ];
    }

    private function getSection(string ...$text): array
    {
        $section = [
            'type' => 'section',
            'fields' => [],
        ];

        foreach ($text as $item) {
            $section['fields'][] = [
                'type' => 'mrkdwn',
                'text' => $item,
            ];
        }

        return $section;
    }
}