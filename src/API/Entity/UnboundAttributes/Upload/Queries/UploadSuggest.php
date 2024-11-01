<?php

declare(strict_types=1);

namespace AvySearch\API\Entity\UnboundAttributes\Upload\Queries;

use Elastic\Elasticsearch\Client;
use AvySearch\API\Entity\UploadQueryInterface;
use AvySearch\DTO\ExternalEntities\Attributes\AttributeInterface;

final class UploadSuggest implements UploadQueryInterface
{
    private AttributeInterface $attr;

    private string $type;
    private string $value;

    public function setAttribute(AttributeInterface $attr): self
    {
        $this->attr = $attr;

        $this->type = $attr->getType();
        $this->value = $attr->getValue();

        return $this;
    }

    public function execSync(Client $elasticClient): void
    {
        $elasticClient->create([
            'id' => uniqid(),
            'index' => 'unbound-attributes_alias',
            'body' => [
                ...$this->attr->__serialize(),
                'value' => $this->value,
                'type' => $this->type,
            ],
        ])->wait();
    }
}
