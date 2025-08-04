<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Category;
use App\Entity\Product;
use App\Enum\ProductStatus;
use App\Notification\Notifier;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;

readonly class ProductListener
{
    public function __construct(
        private LoggerInterface $logger,
        private Notifier        $notifier
    )
    {
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->handleEvent($args, ProductStatus::SAVED);
    }

    private function handleEvent(LifecycleEventArgs $args, ProductStatus $status): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Product) {
            return;
        }

        $action = $status->value;
        $productName = $entity->getName();
        $categories = implode(', ', array_map(fn(Category $c) => $c->getCode(), $entity->getCategories()->toArray()));

        $message = sprintf('Product "%s" has been %s and belongs to categories: %s.', $productName, $action, $categories);

        $this->logger->info($message);

        $subject = "Product status update: $productName";
        $this->notifier->notify($subject, $message);
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->handleEvent($args, ProductStatus::UPDATED);
    }
}
