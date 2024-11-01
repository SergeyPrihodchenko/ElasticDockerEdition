<?php

declare(strict_types=1);

namespace AvySearch;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use AvySearch\Auth\ElasticAuthenticatorInteface;
use AvySearch\API\Migration\Scheme;
use AvySearch\Auth\BaseAuthentication;
use AvySearch\DTO\ExternalEntities\Attributes\Category;
use AvySearch\DTO\ExternalEntities\Attributes\Code;
use AvySearch\DTO\ExternalEntities\Attributes\Filter;
use AvySearch\DTO\ExternalEntities\FileCategory;
use AvySearch\DTO\ExternalEntities\FileData;
use AvySearch\Endpoints\GlobalSearch;
use AvySearch\Endpoints\Upload;
use Dotenv\Dotenv;
use Symfony\Component\HttpClient\Psr18Client;

final class SearchInstance
{
    private Client $elasticClient;

    public function __construct(
        ElasticAuthenticatorInteface $authenticator,
        ClientBuilder $ClientBuilder
    ) {
        $authenticator->authenticate($ClientBuilder);

        $ClientBuilder->setHttpClient(new Psr18Client());

        $this->elasticClient = $ClientBuilder->build();
        $this->elasticClient->setAsync(true);
    }

    public function getElasticClient(): Client
    {
        return $this->elasticClient;
    }

    public function globalSearch(): GlobalSearch
    {
        return new GlobalSearch($this);
    }

    public function upload(): Upload
    {
        return new Upload($this);
    }

    public function runSchemeMigrations(string $migrations_dir = null): bool
    {
        if ($migrations_dir === null) {
            $migrations_dir = __DIR__ . '/../migrations';
        }

        (new Scheme($this->elasticClient, $migrations_dir))->migrateFresh();

        return true;
    }

    public static function main()
    {
        $envpath = __DIR__ . '/../';
        $dotenv = Dotenv::createImmutable($envpath);
        $dotenv->load();

        $url = $_ENV['ELASTIC_CONTAINER_NAME'];
        $port = 9200;
        $user = $_ENV['ELASTIC_USER'];
        $password = $_ENV['ELASTICSEARCH_PASSWORD'];

        $authenticator = new BaseAuthentication("http://$url:$port", $user, $password);
        return new SearchInstance($authenticator, ClientBuilder::create());
    }

    public function createFileData(): FileData
    {
        return new FileData();
    }

    public function createFilterData(string $category, string $title, string $search, string $select): Filter
    {
        return new Filter($category, $title, $search, $select);
    }

    public function createPagetitleData(int $seriaId, int $tableId, string $pagetitle, string $imageUrl): Code
    {
        return new Code($seriaId, $tableId, $pagetitle, $imageUrl);
    }

    public function createCategoryData(int $id, string $title, string $url): Category
    {
        return new Category($id, $title, $url);
    }

    public function createFileCategory($id, $title, $isProductsExists = true): FileCategory
    {
        $category = (new FileCategory())
            ->setId($id)
            ->setTitle($title)
            ->setProductsExists($isProductsExists);

        return $category;
    }
}
