<?php

declare(strict_types=1);

namespace AvySearch\Auth;

use Elastic\Elasticsearch\ClientBuilder;

class BaseAuthentication implements ElasticAuthenticatorInteface
{
    public function __construct(
        private readonly string $host,
        private readonly string $user,
        private readonly string $password
    ) {
    }

    public function authenticate(ClientBuilder $elasticClient): ClientBuilder
    {
        $elasticClient
            ->setHosts([$this->host])
            ->setBasicAuthentication($this->user, $this->password);

        return $elasticClient;
    }
}
