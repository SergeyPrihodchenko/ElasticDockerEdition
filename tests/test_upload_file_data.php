<?php

use AvySearch\API\Entity\File\Upload\Queries\UploadFile;
use AvySearch\Auth\BaseAuthentication;
use AvySearch\SearchInstance;
use AvySearch\Tests\Helpers\TestFileDataGenerator;
use AvySearch\DTO\ExternalEntities\FileCategory;
use Elastic\Elasticsearch\ClientBuilder;

require_once __DIR__ . '/../vendor/autoload.php';

$authenticator = new BaseAuthentication('http://aaavy-elastic-search:9200', 'elastic', 'vnie83924fhkj');
$searchInstance = new SearchInstance($authenticator, ClientBuilder::create());

$testDataGenerator = new TestFileDataGenerator();

$elasticClient = $searchInstance->getElasticClient();

foreach ($testDataGenerator->generator() as $test_file_data) {
    $uploadRequest = (new UploadFile())
        ->setCustomId(228)
        ->setLang($test_file_data->getLanguageAlias())
        ->setFilename($test_file_data->getFilename())
        ->setOriginFilename($test_file_data->getOriginFilename())
        ->setByteSize($test_file_data->getByteSize())
        ->setCatalogText($test_file_data->getText())
        ->setFileType($test_file_data->getFileTypeName());

    $cats = [];
    foreach ($test_file_data->getCategories() as $category) {
        $cats[] = (new FileCategory())
            ->setId($category->getId())
            ->setTitle($category->getTitle())
            ->setProductsExists($category->isProductsExists());
    }
    $uploadRequest->setCategories($cats);

    $catsForGrouping = [];
    foreach ($test_file_data->getChildlessCategories() as $category) {
        $catsForGrouping[] = (new FileCategory())
            ->setId($category->getId())
            ->setTitle($category->getTitle())
            ->setProductsExists($category->isProductsExists());
    }
    $uploadRequest->setCategoriesForGrouping($catsForGrouping);

    $uploadRequest->execSync($elasticClient);
}
