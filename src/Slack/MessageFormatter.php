<?php

declare(strict_types=1);

namespace App\Slack;

class MessageFormatter
{
    public function getHeader(string $text): array
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

    public function getSection(?string ...$text): array
    {
        $section = [
            'type' => 'section',
            'fields' => [],
        ];

        foreach ($text as $item) {
            if (empty($item)) {
                continue;
            }

            $section['fields'][] = [
                'type' => 'mrkdwn',
                'text' => $item,
            ];
        }

        return $section;
    }
}