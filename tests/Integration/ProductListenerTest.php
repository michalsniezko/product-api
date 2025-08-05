<?php

namespace App\Tests\Integration;

use App\Entity\Product;
use App\EventListener\ProductListener;
use App\Message\NotificationMessage;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class ProductListenerTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testPostPersistSendsMessageAndLogs()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info')
            ->with($this->stringContains('Product'));

        $messageBus = $this->createMock(MessageBusInterface::class);
        $messageBus->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function ($message) {
                return $message instanceof NotificationMessage
                    && str_contains($message->subject, 'Product')
                    && str_contains($message->message, 'Product');
            }))
            ->willReturnCallback(function ($message) {
                return new Envelope($message);
            });

        $listener = new ProductListener($logger, $messageBus);

        $product = new Product();
        $product->setName('Test Product');

        $eventArgs = $this->createMock(LifecycleEventArgs::class);
        $eventArgs->method('getObject')->willReturn($product);

        $listener->postPersist($eventArgs);
    }

    /**
     * @throws Exception
     */
    public function testPostUpdateSendsMessageAndLogs()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info')
            ->with($this->stringContains('Product'));

        $messageBus = $this->createMock(MessageBusInterface::class);
        $messageBus->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function ($message) {
                return $message instanceof NotificationMessage
                    && str_contains($message->subject, 'Product')
                    && str_contains($message->message, 'Product');
            }))
            ->willReturnCallback(function ($message) {
                return new Envelope($message);
            });

        $listener = new ProductListener($logger, $messageBus);

        $product = new Product();
        $product->setName('Test Product');

        $eventArgs = $this->createMock(LifecycleEventArgs::class);
        $eventArgs->method('getObject')->willReturn($product);

        $listener->postUpdate($eventArgs);
    }
}
