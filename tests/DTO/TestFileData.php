<?php

declare(strict_types=1);

namespace AvySearch\Tests\DTO;

use AvySearch\Tests\DTO\TestFileCategory;

final class TestFileData
{
    private int $id;
    private string $manufacturerName;
    private string $languageAlias;
    private string $filename;
    private string $originFilename;
    private int $byteSize;
    private string $text;
    private string $fileTypeName;
    private string $mimeType = 'application/pdf';

    /** @var $allCategories list<TestFileCategory> */
    private array $categories = [];
    /** @var $childlessCategories list<TestFileCategory> */
    private array $childlessCategories = [];

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setManufacturerName(string $manufacturerName): self
    {
        $this->manufacturerName = $manufacturerName;

        return $this;
    }

    public function getManufacturerName(): string
    {
        return $this->manufacturerName;
    }

    public function setLanguageAlias(string $languageAlias): self
    {
        $this->languageAlias = $languageAlias;

        return $this;
    }

    public function getLanguageAlias(): string
    {
        return $this->languageAlias;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setOriginFilename(string $originFilename): self
    {
        $this->originFilename = $originFilename;

        return $this;
    }

    public function getOriginFilename(): string
    {
        return $this->originFilename;
    }

    public function setByteSize(int $byteSize): self
    {
        $this->byteSize = $byteSize;

        return $this;
    }

    public function getByteSize(): int
    {
        return $this->byteSize;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setFileTypeName(string $fileTypeName): self
    {
        $this->fileTypeName = $fileTypeName;

        return $this;
    }

    public function getFileTypeName(): string
    {
        return $this->fileTypeName;
    }

    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * @param $category TestFileCategory
     */
    public function appendCategory(TestFileCategory $category): self
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * @param $categories list<TestFileCategory>
     */
    public function setCategories(array $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return list<TestFileCategory>
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @param $category TestFileCategory
     */
    public function appendChildlessCategory(TestFileCategory $category): self
    {
        $this->childlessCategories[] = $category;

        return $this;
    }

    /**
     * @param $categories list<TestFileCategory>
     */
    public function setChildlessCategories(array $categories): self
    {
        $this->childlessCategories = $categories;

        return $this;
    }

    /**
     * @return list<TestFileCategory>
     */
    public function getChildlessCategories(): array
    {
        return $this->childlessCategories;
    }
}
