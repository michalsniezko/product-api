<?php

namespace App\Tests\Unit;

use App\Entity\Category;
use App\Entity\Product;
use App\EventListener\ProductListener;
use App\Message\NotificationMessage;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

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

        $messageBus = $this->createMock(MessageBusInterface::class);
        $messageBus->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(fn($message) => $message instanceof NotificationMessage
                && str_contains($message->subject, 'Product')
                && str_contains($message->message, 'Product')))
            ->willReturnCallback(fn($message) => new Envelope($message));

        $listener = new ProductListener($logger, $messageBus);

        $product = new Product();
        $product->setName('Test Product');

        $category = new Category();
        $category->setCode('TEST-CAT');

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

        $messageBus = $this->createMock(MessageBusInterface::class);
        $messageBus->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(fn($message) => $message instanceof NotificationMessage
                && str_contains($message->subject, 'Product')
                && str_contains($message->message, 'Product')))
            ->willReturnCallback(fn($message) => new Envelope($message));

        $listener = new ProductListener($logger, $messageBus);

        $product = new Product();
        $product->setName('Test Product');

        $category = new Category();
        $category->setCode('TEST-CAT');

        $eventArgs = $this->createMock(LifecycleEventArgs::class);
        $eventArgs->method('getObject')->willReturn($product);

        $listener->postUpdate($eventArgs);
    }
}
