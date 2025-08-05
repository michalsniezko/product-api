<?php
declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Category;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CategoryDeletionListener
{
    const string MESSAGE = 'Cannot remove category because at least one product is assigned to it.';

    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Category) {
            return;
        }

        if (!$entity->getProducts()->isEmpty()) {
            throw new BadRequestHttpException(self::MESSAGE);
        }
    }
}
