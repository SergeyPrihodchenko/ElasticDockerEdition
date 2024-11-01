<?php

declare(strict_types=1);

namespace AvySearch\API\Entity\File\Upload\Queries;

use Elastic\Elasticsearch\Client;
use AvySearch\DTO\ExternalEntities\FileCategory;
use AvySearch\API\Entity\UploadQueryInterface;

final class UploadFile implements UploadQueryInterface
{
    private ?string $customId;
    private string $filename;
    private string $originFilename;
    private int $byteSize;
    private string $lang;
    private string $catalogText;
    private string $fileType;
    /**
     * @var FileCategory[] $categories
     */
    private array $categories;
    /**
     * @var FileCategory[] $categoriesForGrouping
     */
    private array $categoriesForGrouping;

    public function setCustomId(?string $id): self
    {
        $this->customId = $id;

        return $this;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function setOriginFilename(string $originFilename): self
    {
        $this->originFilename = $originFilename;

        return $this;
    }

    public function setByteSize(int $byteSize): self
    {
        $this->byteSize = $byteSize;

        return $this;
    }

    public function setLang(string $lang): self
    {
        $this->lang = $lang;

        return $this;
    }

    public function setCatalogText(string $catalogText): self
    {
        $this->catalogText = $catalogText;

        return $this;
    }

    public function setFileType(string $fileType): self
    {
        $this->fileType = $fileType;

        return $this;
    }

     /**
     * @param non-empty-array<FileCategory> $categories
     */
    public function setCategories(array $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @param non-empty-array<FileCategory> $categoriesForGrouping
     */
    public function setCategoriesForGrouping(array $categoriesForGrouping): self
    {
        $this->categoriesForGrouping = $categoriesForGrouping;

        return $this;
    }

    public function execSync(Client $elasticClient): void
    {
        $categoriesForGrouping_ids = [];
        $global_have_products = false;

        foreach ($this->categoriesForGrouping as $seria) {
            $categoriesForGrouping_ids[] = $seria->getId();
            $global_have_products = $seria->isProductsExists() ? true : $global_have_products;
        }

        $allCategories_ids = [];
        $allCategories_titles = [];

        foreach ($this->categories as $category) {
            /** @var FileCategory $category*/
            $allCategories_ids[] = $category->getId();
            $allCategories_titles[] = $category->getTitle();
        }

        $elasticClient->create([
            'id' => uniqid(),
            'index' => 'files',
            'body' => [
                'custom-id'             => $this->customId,
                'text-content'          => $this->catalogText,
                'categories-full-text'  => $allCategories_titles,
                'file-name'             => $this->filename,
                'origin-file-name'      => $this->originFilename,
                'lang'                  => $this->lang,
                'file-size'             => $this->byteSize,
                'file-type'             => $this->fileType,
                'exists-products'       => $global_have_products,
                'categories'            => $allCategories_ids,
                'series'                => $categoriesForGrouping_ids,
            ]
        ])->wait();

        foreach ($this->categoriesForGrouping as $seria) {
            $elasticClient->create([
                'id' => uniqid(),
                'index' => 'files-seria-' . $seria->getId(),
                'body' => [
                    'custom-id'             => $this->customId,
                    'text-content'          => $this->catalogText,
                    'categories-full-text'  => $allCategories_titles,
                    'file-name'             => $this->filename,
                    'origin-file-name'      => $this->originFilename,
                    'lang'                  => $this->lang,
                    'file-size'             => $this->byteSize,
                    'file-type'             => $this->fileType,
                    'exists-products'       => $seria->isProductsExists(),
                    'categories'            => $allCategories_ids,
                    'series'                => $seria->getId(),
                ]
            ])->wait();
        }
    }
}
