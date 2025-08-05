<?php

declare(strict_types=1);

namespace App\Notification;

interface NotificationInterface
{
    public function notify(string $subject, string $message): void;
}
