<?php

declare(strict_types=1);

namespace AvySearch\DTO\ExternalEntities;

final class FileCategory
{
    /**
     * @var int $id - числовой идентификатор категории
     */
    private int $id;
    /**
     * @var string $title - название категории
     */
    private string $title;
    /**
     * @var bool $productsExists - есть ли продукция в этой категории? true - да; false - нет
     */
    private bool $productsExists;

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setProductsExists(bool $productsExists): self
    {
        $this->productsExists = $productsExists;

        return $this;
    }

    public function isProductsExists(): bool
    {
        return $this->productsExists;
    }
}
