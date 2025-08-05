<?php

declare(strict_types=1);

namespace App\Notification;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

readonly class MailNotification implements NotificationInterface
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger,
        private string          $fromEmail,
        private string          $toEmail
    )
    {
    }

    public function notify(string $subject, string $message): void
    {
        $email = new Email()
            ->from($this->fromEmail)
            ->to($this->toEmail)
            ->subject($subject)
            ->html($message)
            ->text(strip_tags($message));

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Failed to send notification email: ' . $e->getMessage());
        }
    }
}
