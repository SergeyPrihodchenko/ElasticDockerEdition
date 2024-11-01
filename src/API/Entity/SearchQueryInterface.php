<?php

declare(strict_types=1);

namespace AvySearch\API\Entity;

use Elastic\Elasticsearch\Client;
use Http\Promise\Promise;

interface SearchQueryInterface
{
    public function sendAsync(Client $elasticClient): Promise;

    public function await(): array;

}
