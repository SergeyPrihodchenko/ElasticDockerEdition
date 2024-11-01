<?php

use AvySearch\Auth\BaseAuthentication;
use AvySearch\DTO\ExternalEntities\Attributes\Category;
use AvySearch\DTO\ExternalEntities\Attributes\Code;
use AvySearch\DTO\ExternalEntities\Attributes\Filter;
use AvySearch\DTO\ExternalEntities\Attributes\Series;
use AvySearch\SearchInstance;
use AvySearch\Tests\Helpers\TestUnboundAttributesDataGenerator;
use Elastic\Elasticsearch\ClientBuilder;

require_once __DIR__ . '/../vendor/autoload.php';

$authenticator = new BaseAuthentication('http://aaavy-elastic-search:9200', 'elastic', 'vnie83924fhkj');
$searchInstance = new SearchInstance($authenticator, ClientBuilder::create());

$testDataGenerator = new TestUnboundAttributesDataGenerator();

$attrs = [
    'category' => Category::class,
    'code' => Code::class,
    'filter' => Filter::class,
    'series' => Series::class,
];

foreach ($testDataGenerator->generator() as $test_product_suggest_data) {
    [$type, $value] = $test_product_suggest_data;

    $attr = new $attrs[$type];
    switch ($type){
        
        case 'category':
            $attr->setTitle($value)
                ->setSubtitle('subtitle_' . $value);
            break;

        case 'code':
            $attr->setTitle($value)
                ->setPrice(893288.84)
                ->setAmount(528);
            break;

        case 'filter':
            $attr->setTitle($value)
                ->setSubtitle('subtitle_' . $value);
            break;

        case 'series':
            $attr->setTitle($value)
                ->setSubtitle('subtitle_' . $value)
                ->setCharacteristics([[
                    'title' => 'title характеристики',
                    'subtitle' => 'subtitle характеристики'
                ]]);
            break;
    }
        
    $searchInstance->upload()->UnboundAttribute($attr);
}
