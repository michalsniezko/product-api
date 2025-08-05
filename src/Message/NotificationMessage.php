<?php

declare(strict_types=1);

namespace App\Message;

class NotificationMessage
{
    public function __construct(
        public string $subject,
        public string $message
    )
    {
    }
}
