<?php
declare(strict_types=1);

namespace App\Notification;

class Notifier
{
    /** @var NotificationInterface[] */
    private array $channels = [];

    public function __construct(iterable $channels)
    {
        foreach ($channels as $channel) {
            $this->channels[] = $channel;
        }
    }

    public function notify(string $subject, string $message): void
    {
        foreach ($this->channels as $channel) {
            $channel->notify($subject, $message);
        }
    }
}

