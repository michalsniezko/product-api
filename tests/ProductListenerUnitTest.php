<?php

namespace App\Tests;


use App\Entity\Product;
use App\EventListener\ProductListener;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

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

        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects($this->once())
            ->method('send')
            ->with($this->callback(function ($email) {
                return $email instanceof Email
                    && str_contains($email->getSubject(), 'Product');
            }));

        $listener = new ProductListener($logger, $mailer);

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

        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects($this->once())
            ->method('send')
            ->with($this->callback(function ($email) {
                return $email instanceof Email
                    && str_contains($email->getSubject(), 'Product');
            }));

        $listener = new ProductListener($logger, $mailer);

        $product = new Product();
        $product->setName('Test Product');

        $eventArgs = $this->createMock(LifecycleEventArgs::class);
        $eventArgs->method('getObject')->willReturn($product);

        $listener->postUpdate($eventArgs);
    }

}
