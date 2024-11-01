<?php

declare(strict_types=1);

namespace AvySearch\Tests\Helpers;

use RuntimeException;
use AvySearch\Tests\DTO\TestFileCategory;
use AvySearch\Tests\DTO\TestFileData;

final class TestFileDataGenerator
{
    private readonly string $csvFileDataPath;
    private readonly string $csvFileCategoryDataPath;

    private array $fileCategories;

    public function __construct(
        string $csvFileDataPath = null,
        string $csvFileCategoryDataPath = null
    ) {
        if ($csvFileDataPath === null) {
            $this->csvFileDataPath = __DIR__ . '/../.testdata/TestFileDataGenerator/files.csv';
        }

        if ($csvFileCategoryDataPath === null) {
            $this->csvFileCategoryDataPath = __DIR__ . '/../.testdata/TestFileDataGenerator/file_category.csv';
        }

        foreach ([$this->csvFileDataPath, $this->csvFileCategoryDataPath] as $file) {
            if (!is_file($file)) {
                throw new RuntimeException(sprintf("Не удалось найти файл '%s'", $file));
            }
        }

        $this->loadAllFileCategories();
    }

    public function generator(): \Generator
    {
        $fileStream = fopen($this->csvFileDataPath, 'r');

        // пропускаем заголовки
        fgetcsv($fileStream, 0, ';', '"');

        while (($fileSrc = fgetcsv($fileStream, 0, ';', '"')) !== false) {
            yield $this->mapRawFileData($fileSrc);
        }
    }

    private function mapRawFileData(array $rawData): TestFileData
    {
        $id                 = (int)$rawData[0];
        $manufacturerName   = $rawData[1];
        $languageAlias      = $rawData[2];
        $filename           = $rawData[3];
        $originFilename     = $rawData[4];
        $byteSize           = (int)$rawData[5];
        $text               = $rawData[6];
        $fileTypeName       = $rawData[7];
        $mimeType           = $rawData[8];

        /**
         * @var $allCategories list<TestFileCategory>
         */
        $categories = $this->fileCategories[$id]['categories'] ?? [];
        /**
         * @var $childlessCategories list<TestFileCategory>
         */
        $childlessCategories = $this->fileCategories[$id]['childlessCategories'] ?? [];

        return (new TestFileData())
            ->setId($id)
            ->setManufacturerName($manufacturerName)
            ->setLanguageAlias($languageAlias)
            ->setFilename($filename)
            ->setOriginFilename($originFilename)
            ->setByteSize($byteSize)
            ->setText($text)
            ->setFileTypeName($fileTypeName)
            ->setMimeType($mimeType)
            ->setCategories($categories)
            ->setChildlessCategories($childlessCategories);
    }

    private function loadAllFileCategories(): void
    {
        $this->fileCategories = [];

        $fileStream = fopen($this->csvFileCategoryDataPath, 'r');

        // skip header rows
        \fgetcsv($fileStream, 0, ';');

        while (($row = \fgetcsv($fileStream, 0, ';')) !== false) {
            $file_id        = (int)$row[0];
            $id             = (int)$row[2];
            $parentId       = (int)$row[3];
            $title          = (string)$row[4];
            $productsExists  = (bool)$row[6];

            $category = (new TestFileCategory())
                ->setFileId($file_id)
                ->setId($id)
                ->setParentId($parentId)
                ->setTitle($title)
                ->setProductsExists($productsExists);

            $this->fileCategories[$file_id]['categories'][] = $category;
        }

        foreach ($this->fileCategories as $file_id => $fileCategoriesData) {
            $childlessCategories = $this->getСhildlessCategories($fileCategoriesData['categories']);
            $this->fileCategories[$file_id]['childlessCategories'] = $childlessCategories;
        }
    }

    /**
     * @param $categories list<TestFileCategory>
     *
     * @return list<TestFileCategory>
     */
    private function getСhildlessCategories(array $categories): array
    {
        $childlessCategories = [];

        $parentIds = array_map(
            static fn (TestFileCategory $category) => $category->getParentId(),
            $categories
        );

        foreach ($categories as $category) {
            if (!in_array($category->getId(), $parentIds)) {
                $childlessCategories[] = $category;
            }
        }

        return $childlessCategories;
    }
}
