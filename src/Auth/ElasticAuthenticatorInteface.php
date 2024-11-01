<?php

declare(strict_types=1);

namespace AvySearch\Auth;

use Elastic\Elasticsearch\ClientBuilder;

interface ElasticAuthenticatorInteface
{
    public function authenticate(ClientBuilder $elasticClient): ClientBuilder;
}
