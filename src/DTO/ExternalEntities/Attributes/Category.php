<?php

declare(strict_types=1);

namespace AvySearch\DTO\ExternalEntities\Attributes;

final class Category implements AttributeInterface
{



    public function __construct(
        private int $seriaId,
        private string $value,
        private string $url,
    ){}



    public function getType(): string
    {
        return 'category';
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __serialize(): array
    {
        return [
            'seriaId' => $this->seriaId,
            'value' => $this->value,
            'url' => $this->url,
        ];
    }
}
