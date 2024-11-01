<?php

declare(strict_types=1);

namespace AvySearch\API\Migration;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ElasticsearchException;
use Elastic\Transport\Exception\NoNodeAvailableException;
use Exception;
use RuntimeException;

final class Scheme
{
    private readonly string $migrations_dir;
    private readonly Client $elasticClient;

    public function __construct(Client $elasticClient, string $migrations_dir)
    {
        if (!is_dir($migrations_dir)) {
            throw new \RuntimeException("Не удалось найти директорию '$migrations_dir'");
        }

        $this->migrations_dir = rtrim($migrations_dir, '\\/');
        $this->elasticClient = $elasticClient;
    }

    /**
     * Подключает файлы миграции индексов и запускает их.
     * В миграциях нужно указывать параметр 'method' - это должен быть один из методов класса
     * Elastic\Elasticsearch\Endpoints\Indices
     *
     * @throws ElasticsearchException
     * @throws RuntimeException
     */
    public function migrateFresh(): void
    {
        $migration_pattern = $this->migrations_dir . '/*.php' ;
        $migrations = [];

        foreach (glob($migration_pattern) as $migration_file) {
            $migration = require $migration_file;

            if (!isset($migration['up']['method']) || empty($migration['up']['method'])) {
                $migrationFilename = basename($migration_file);
                throw new RuntimeException("В файле миграции '$migrationFilename' не установлен параметр 'method' в `up` свойстве");
            }

            if (!isset($migration['down']['method']) || empty($migration['down']['method'])) {
                $migrationFilename = basename($migration_file);
                throw new RuntimeException("В файле миграции '$migrationFilename' не установлен параметр 'method' в `down` свойстве");
            }

            $migrations[] = $migration;
        }

        foreach ($migrations as $migration) {
            try {
                $migrationDown = $migration['down'];
                $migrationDownMethod = $migrationDown['method'];
                $this->elasticClient->indices()->$migrationDownMethod($migration['down'])->wait();
            } catch (ClientResponseException|NoNodeAvailableException $e) {
                echo "Warning: `down` migration fails with error: " . $e->getMessage() . "\n";
            }

            $migrationUp = $migration['up'];
            $migrationUpMethod = $migrationUp['method'];
            
            $this->elasticClient
                ->indices()
                ->$migrationUpMethod($migration['up'])
                ->wait();
        }
    }
}
