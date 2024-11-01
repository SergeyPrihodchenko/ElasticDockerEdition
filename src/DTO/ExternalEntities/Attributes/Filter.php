<?php

declare(strict_types=1);

namespace AvySearch\DTO\ExternalEntities\Attributes;

final class Filter implements AttributeInterface
{

    public function __construct(
        private string $category,
        private string $title,
        private string $search,
        private string $select,
    ) {
    }

    // Тип в поисковых подсказках
    public function getType(): string
    {
        return 'filter';
    }

    // Значение в подсказках
    public function getValue(): string
    {
        return $this->title;
    }

    public function __serialize(): array
    {
        return [
            'category' => $this->category,
            'title' => $this->title,
            'search' => $this->search,
            'select' => $this->select,
        ];
    }
}
