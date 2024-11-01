<?php

use AvySearch\Auth\BaseAuthentication;
use AvySearch\SearchInstance;
use Elastic\Elasticsearch\ClientBuilder;

require_once __DIR__ . '/../vendor/autoload.php';

$authenticator = new BaseAuthentication('http://localhost:9200', 'elastic', 'vnie83924fhkj');
$searchInstance = new SearchInstance($authenticator, ClientBuilder::create());

$result = $searchInstance->getFileEntity()->search()->searchSeriesCollapsed('экранный зажим кабеля');

var_dump($result['items']);
