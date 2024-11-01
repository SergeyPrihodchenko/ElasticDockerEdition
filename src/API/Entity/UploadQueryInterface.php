<?php

declare(strict_types=1);

namespace AvySearch\API\Entity;

use Elastic\Elasticsearch\Client;

interface UploadQueryInterface
{
    public function execSync(Client $elasticClient): void;

}
