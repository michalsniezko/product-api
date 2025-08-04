<?php
declare(strict_types=1);

namespace App\EventListener;


use App\Entity\Product;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ProductListener
{
    private LoggerInterface $logger;
    private MailerInterface $mailer;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->logAndNotify($args);
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->logAndNotify($args);
    }

    private function logAndNotify(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof Product) {
            return;
        }

        $this->logger->info(sprintf('Product %s has been saved.', $entity->getName()));

        $email = (new Email())
            ->from('no-reply@example.com')
            ->to('admin@example.com')
            ->subject('Product saved')
            ->text(sprintf('Product "%s" has been saved.', $entity->getName()));

         $this->mailer->send($email);
    }
}
