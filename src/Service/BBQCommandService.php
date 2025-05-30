<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Queue;
use App\Entity\QueuedUser;
use App\Repository\QueuedUserRepository;
use App\Slack\MessageFormatter;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

readonly class BBQCommandService
{
    public function __construct(
        private QueuedUserRepository $queuedUserRepository,
        private EntityManagerInterface $entityManager,
        private MessageFormatter $messageFormatter,
    ) {}

    public function joinQueue(Queue $queue, string $userId): JsonResponse
    {
        $user = $this->queuedUserRepository->findUserInQueue($queue, $userId);

        if ($user !== null) {
            return new JsonResponse([
                'blocks' => [
                    $this->messageFormatter->getHeader(':doh: You are already in the '.$queue->name.' queue!')
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

        $this->entityManager->refresh($user);

        return new JsonResponse([
            'blocks' => [
                $this->messageFormatter->getHeader('Queue joined successfully!'),
                $this->messageFormatter->getSection(
                    '*Queue*'.PHP_EOL.$queue->name,
                    $queue->expiryInMinutes !== null ? '*Expiry length*'.PHP_EOL.($queue->expiryInMinutes).' minutes' : null,
                    '*Your place*'.PHP_EOL.$user->getPlaceInQueue(),
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
                    $this->messageFormatter->getHeader(':doh: You never joined the ' . $queue->name . ' queue!')
                ]
            ]);
        }

        $user->deletedAt = new DateTimeImmutable();

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse([
            'blocks' => [
                $this->messageFormatter->getHeader('You\'ve successfully left the ' . $queue->name . ' queue!'),
            ]
        ]);
    }

    public function list(Queue $queue): JsonResponse
    {
        $users = $this->queuedUserRepository->findBy([
            'queue' => $queue,
            'deletedAt' => null,
        ]);

        if (empty($users)) {
            return new JsonResponse([
                'blocks' => [
                    $this->messageFormatter->getHeader('Queue for '.$queue->name.' is empty. :tada:'),
                ]
            ]);
        }

        $key = 1;

        return new JsonResponse([
            'blocks' => [
                $this->messageFormatter->getHeader('Queue for '.$queue->name.':'),
                $this->messageFormatter->getSection(
                    implode(PHP_EOL, array_map(
                        static function (QueuedUser $user) use (&$key): string
                        {
                            return ($key++) . '. ' . $user->getUserLink();
                        },
                        $users
                    ))
                )
            ]
        ]);
    }

    public function unrecognisedCommand(string $text): JsonResponse
    {
        return new JsonResponse([
            'blocks' => [
                $this->messageFormatter->getHeader(':doh: Unrecognised command \''.$text.'\'.'),
            ],
        ]);
    }

    public function queueNotFound(string $queue): JsonResponse
    {
        return new JsonResponse([
            'blocks' => [
                $this->messageFormatter->getHeader(':doh: Queue \''.$queue.'\' does not exist.')
            ]
        ]);
    }
}