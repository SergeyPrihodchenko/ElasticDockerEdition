<?php

declare(strict_types=1);

namespace AvySearch\DTO\ExternalEntities\Attributes;

final class Code implements AttributeInterface
{

    function __construct(
        private int $seriaId,
        private int $tableId,
        private string $value,
        private string $imageUrl,
    ) {}

    public function getType(): string
    {
        return 'code';
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __serialize(): array
    {
        return [
            'value' => $this->value,
            'tableId' => $this->tableId,
            'seriaId' => $this->seriaId,
            'imageUrl' => $this->imageUrl,
        ];
    }
}
