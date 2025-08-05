<?php
declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\NotificationMessage;
use App\Notification\Notifier;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class NotificationMessageHandler
{
    public function __construct(private Notifier $notifier)
    {
    }

    public function __invoke(NotificationMessage $message): void
    {
        $this->notifier->notify($message->subject, $message->message);
    }
}
