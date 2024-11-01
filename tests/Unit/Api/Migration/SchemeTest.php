<?php

declare(strict_types=1);

namespace AvySearch\Tests\Unit\Api\Migration;

use AvySearch\Tests\AbstractTestCase;
use Elastic\Elasticsearch\Client;
use AvySearch\API\Migration\Scheme;
use Elastic\Elasticsearch\Endpoints\Indices;

final class SchemeTest extends AbstractTestCase
{
    private string $tmpDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tmpDir = sys_get_temp_dir();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Чистим директорию с временными файлами от тестовых файлов
        $files = glob($this->tmpDir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function testConstructedProperly(): void
    {
        $elasticClient = $this->createMock(Client::class);
        $dir = $this->tmpDir . '/';

        $SchemeObject = new Scheme($elasticClient, $dir);

        $SchemeReflection = new \ReflectionClass(Scheme::class);
        $dir_property = $SchemeReflection->getProperty('migrations_dir')->getValue($SchemeObject);
        $elasticClient_property = $SchemeReflection->getProperty('elasticClient')->getValue($SchemeObject);

        $this->assertSame(rtrim($dir, '\\/'), $dir_property);
        $this->assertSame($elasticClient, $elasticClient_property);
    }

    public function testConstructErrorIfDirNotExists()
    {
        $elasticClient = $this->createMock(Client::class);
        $dir = '/unexisting_dir';

        $this->expectException(\RuntimeException::class);

        new Scheme($elasticClient, $dir);
    }

    public function testErrorIfMigrationArrayHasntMethod()
    {
        $migrationsFiles = [];

        $migration_1_path = sprintf('%s/%s', $this->tmpDir, 'migration_1.php');
        $migration_2_path = sprintf('%s/%s', $this->tmpDir, 'migration_2.php');

        \file_put_contents($migration_1_path, "<?php return ['up' => ['method' => 'index', 'body' => []], 'down' => ['method' => 'delete', 'body' => []]];");
        \file_put_contents($migration_2_path, "<?php return ['up' => ['body' => []], 'down' => ['method' => 'deleteIndexTemplate', 'body' => []]];");

        $elasticClient = $this->createMock(Client::class);
        $elasticClient->expects($this->never())
            ->method('indices');

        $SchemeObject = new Scheme($elasticClient, $this->tmpDir);

        $this->expectException(\RuntimeException::class);

        $SchemeObject->migrateFresh();
    }

    public function testMigrateCallsProperlyApiMethods()
    {
        $migration_1_path = sprintf('%s/%s', $this->tmpDir, 'migration_1.php');
        $migration_2_path = sprintf('%s/%s', $this->tmpDir, 'migration_2.php');

        \file_put_contents($migration_1_path, "<?php return ['up' => ['method' => 'create', 'body' => []], 'down' => ['method' => 'delete', 'body' => []]];");
        \file_put_contents($migration_2_path, "<?php return ['up' => ['method' => 'create', 'body' => []], 'down' => ['method' => 'delete', 'body' => []]];");

        $Indicies = $this->createMock(Indices::class);
        $Indicies->expects($this->exactly(2))
            ->method('create');

        $elasticClient = $this->createMock(Client::class);
        $elasticClient->expects($this->exactly(4))
            ->method('indices')
            ->willReturnCallback(function () use ($Indicies) {
                return $Indicies;
            });

        $SchemeObject = new Scheme($elasticClient, $this->tmpDir);
        $SchemeObject->migrateFresh();
    }
}
