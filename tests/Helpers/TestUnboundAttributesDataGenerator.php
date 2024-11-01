<?php

declare(strict_types=1);

namespace AvySearch\Tests\Helpers;

use RuntimeException;

final class TestUnboundAttributesDataGenerator
{
    private readonly string $csvFileDataPath;

    public function __construct(string $csvFilesPath = null)
    {
        if ($csvFilesPath === null) {
            $this->csvFileDataPath = __DIR__ . '/../.testdata/TestUnboundAttributesDataGenerator';
        } else {
            $this->csvFileDataPath = \rtrim($csvFilesPath, '\\/');
        }

        if (!is_dir($this->csvFileDataPath)) {
            throw new RuntimeException(sprintf("Не удалось найти директорию с тестовыми данными '%s'", $csvFilesPath));
        }
    }

    public function generator(): \Generator
    {
        foreach (glob($this->csvFileDataPath . '/*.csv') as $file) {
            $fileStream = fopen($file, 'r');

            // пропускаем заголовки
            fgetcsv($fileStream, 0, ';', '"');

            while (($fileSrc = fgetcsv($fileStream, 0, ';', '"')) !== false) {
                $type = \pathinfo($file, PATHINFO_FILENAME);
                $value = $fileSrc[0];

                yield [$type, $value];
            }
        }
    }
}
