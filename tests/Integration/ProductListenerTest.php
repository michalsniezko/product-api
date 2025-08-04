<?php

namespace App\Tests\Integration;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Repository\ProductRepository;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ProductListenerTest extends ApiTestCase
{
    public static function setUpBeforeClass(): void
    {
        self::bootKernel();
        $entityManager = self::getContainer()->get('doctrine')->getManager();

        $dbFile = '/tmp/test.sqlite';
        if (file_exists($dbFile)) {
            unlink($dbFile);
        }

        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();

        if (!empty($metadata)) {
            $schemaTool = new SchemaTool($entityManager);
            $schemaTool->dropSchema($metadata);
            $schemaTool->createSchema($metadata);
        }
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testProductPostTriggersListener(): void
    {
        $client = static::createClient();

        $response = $client->request('POST', '/api/categories', [
            'json' => [
                'code' => 'TEST',
                'name' => 'Test category'
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $categoryIri = $response->toArray()['@id'];

        $client->request('POST', '/api/products', [
            'json' => [
                'name' => 'Test product',
                'price' => 100,
                'categories' => [$categoryIri]
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $logContents = file_get_contents(__DIR__ . '/../../var/log/test.log');
        $this->assertStringContainsString('Product "Test product" has been saved', $logContents);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testProductPutTriggersListener(): void
    {
        $client = static::createClient();

        /** @var ProductRepository $repo */
        $repo = static::getContainer()->get(ProductRepository::class);
        $product = $repo->findOneBy(['name' => 'Test product']);
        $this->assertNotNull($product);

        $client->request('PATCH', '/api/products/' . $product->getId(), [
            'json' => [
                'name' => 'Test product updated'
            ]
        ]);

        $this->assertResponseIsSuccessful();

        $logContents = file_get_contents(__DIR__ . '/../../var/log/test.log');
        $this->assertStringContainsString('Product "Test product updated" has been updated', $logContents);
    }
}
