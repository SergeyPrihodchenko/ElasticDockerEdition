<?php

declare(strict_types=1);

namespace AvySearch\DTO\ExternalEntities;

use AvySearch\DTO\ExternalEntities\FileCategory;

final class FileData
{
    /**
     * @var ?string $customId - идентификатор документа, который будет отдаваться вместе с результатами поиска
     */
    private ?string $customId = null;

    /**
     * @var string $languageAlias - Основной язык текста файла. Например 'rus' или 'eng'
     */
    private string $languageAlias;

    /**
     * @var string $filename - Имя файла, под которым он хранится в файловой системе. Указывается вместе с расширением
     */
    private string $filename;

    /**
     * @var string $originFilename - Человекочитаемое имя файла, которое можно отображать для пользователя.
     */
    private string $originFilename;

    /**
     * @var int $byteSize - Размер файла в байтах
     */
    private int $byteSize;

    /**
     * @var string $text - Текстовое содержимое файла
     */
    private string $text;

    /**
     * @var string $fileType - Тип файла. Например 'catalog', если это каталог продукции
     */
    private string $fileTypeName;

    private string $mimeType = 'application/pdf';

    /**
     * @var FileCategory[] - все категории, привязанные к файлу
     */
    private array $categories = [];
    /**
     * @var FileCategory[] - все категории, которые не имеют подкатегорий (как правило - категории-серии)
     */
    private array $categoriesForGrouping = [];

    public function setCustomId(string $customId): self
    {
        $this->customId = $customId;
        return $this;
    }

    public function getCustomId(): ?string
    {
        return $this->customId;
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
     * @param $category FileCategory
     */
    public function appendCategory(FileCategory $category): self
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * @param $categories list<FileCategory>
     */
    public function setCategories(array $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return list<FileCategory>
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @param $category FileCategory
     */
    public function appendCategoryForGrouping(FileCategory $category): self
    {
        $this->categoriesForGrouping[] = $category;

        return $this;
    }

    /**
     * @param $categories list<FileCategory>
     */
    public function setCategoriesForGrouping(array $categories): self
    {
        $this->categoriesForGrouping = $categories;

        return $this;
    }

    /**
     * @return list<FileCategory>
     */
    public function getCategoriesForGrouping(): array
    {
        return $this->categoriesForGrouping;
    }
}
