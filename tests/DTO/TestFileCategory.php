<?php

declare(strict_types=1);

namespace AvySearch\Tests\DTO;

final class TestFileCategory
{
    private int $id;
    private int $fileId;
    private ?int $parentId;
    private string $title;
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

    public function setFileId(int $fileId): self
    {
        $this->fileId = $fileId;

        return $this;
    }

    public function getFileId(): int
    {
        return $this->fileId;
    }

    public function setParentId(?int $parentId): self
    {
        $this->parentId = $parentId;

        return $this;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
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
