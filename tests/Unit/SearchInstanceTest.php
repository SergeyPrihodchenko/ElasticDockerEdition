<?php

declare(strict_types=1);

namespace AvySearch\Tests\Unit;

use AvySearch\Tests\AbstractTestCase;
use Elastic\Elasticsearch\Client;
use AvySearch\SearchInstance;
use Elastic\Elasticsearch\ClientBuilder;
use AvySearch\Auth\ElasticAuthenticatorInteface;
use AvySearch\API\Entity\File\FileEntity;

final class SearchInstanceTest extends AbstractTestCase
{
    public function testConstructedProperly(): void
    {
        $elasticClient = $this->createMock(Client::class);

        $clientBuilder = $this->getMockBuilder(ClientBuilder::class)->setMethods(['build'])->getMock();
        $clientBuilder->expects($this->once())->method('build')->willReturn($elasticClient);

        $authentificatorMock = $this->getMockBuilder(ElasticAuthenticatorInteface::class)
            ->setMethods(['authenticate'])
            ->getMock();

        $authentificatorMock->expects($this->once())
            ->method('authenticate')
            ->with($clientBuilder)
            ->willReturn($clientBuilder);

        $searchInstance = new SearchInstance($authentificatorMock, $clientBuilder);

        $searchInstanceReflection = new \ReflectionClass(SearchInstance::class);
        $elasticClient_property = $searchInstanceReflection->getProperty('elasticClient')->getValue($searchInstance);

        $this->assertSame($elasticClient, $elasticClient_property);
    }

    public function testGetFileEntityReturnsFileEntity()
    {
        $authentificatorMock = $this->createMock(ElasticAuthenticatorInteface::class);
        $clientBuilder = $this->createMock(ClientBuilder::class);

        $searchInstance = new SearchInstance($authentificatorMock, $clientBuilder);

        $this->assertInstanceOf(FileEntity::class, $searchInstance->getFileEntity());
    }

    public function testRunSchemeMigrationsWithoutErrors()
    {
        $authentificatorMock = $this->createMock(ElasticAuthenticatorInteface::class);
        $clientBuilder = $this->createMock(ClientBuilder::class);

        $searchInstance = new SearchInstance($authentificatorMock, $clientBuilder);

        $this->assertTrue($searchInstance->runSchemeMigrations());
    }
}
