<?php

namespace App\Tests;

use App\Entity\Product;
use App\EventListener\ProductListener;
use App\Notification\Notifier;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ProductListenerUnitTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testPostPersistSendsMailAndLogs()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info')
            ->with($this->stringContains('Product'));

        $notifier = $this->createMock(Notifier::class);
        $notifier->expects($this->once())
            ->method('notify')
            ->with($this->callback(function ($message) {
                return is_string($message) && str_contains($message, 'Product');
            }));

        $listener = new ProductListener($logger, $notifier, 'from@test.com', 'to@test.com');

        $product = new Product();
        $product->setName('Test Product');

        $eventArgs = $this->createMock(LifecycleEventArgs::class);
        $eventArgs->method('getObject')->willReturn($product);

        $listener->postPersist($eventArgs);
    }

    /**
     * @throws Exception
     */
    public function testPostUpdateSendsMailAndLogs()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info')
            ->with($this->stringContains('Product'));

        $notifier = $this->createMock(Notifier::class);
        $notifier->expects($this->once())
            ->method('notify')
            ->with($this->callback(function ($message) {
                return is_string($message) && str_contains($message, 'Product');
            }));

        $listener = new ProductListener($logger, $notifier, 'from@test.com', 'to@test.com');

        $product = new Product();
        $product->setName('Test Product');

        $eventArgs = $this->createMock(LifecycleEventArgs::class);
        $eventArgs->method('getObject')->willReturn($product);

        $listener->postUpdate($eventArgs);
    }
}
