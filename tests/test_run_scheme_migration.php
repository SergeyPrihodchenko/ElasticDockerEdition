<?php

use AvySearch\Auth\BaseAuthentication;
use AvySearch\SearchInstance;
use Elastic\Elasticsearch\ClientBuilder;

require_once __DIR__ . '/../vendor/autoload.php';

$authenticator = new BaseAuthentication('http://aaavy-elastic-search:9200', 'elastic', 'vnie83924fhkj');
$searchInstance = new SearchInstance($authenticator, ClientBuilder::create());

$searchInstance->runSchemeMigrations();
