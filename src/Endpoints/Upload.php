<?php

declare(strict_types=1);

namespace AvySearch\Endpoints;

use AvySearch\API\Entity\File\Upload\Queries\UploadFile;
use AvySearch\API\Entity\UnboundAttributes\Upload\Queries\UploadSuggest;
use AvySearch\DTO\ExternalEntities\Attributes\AttributeInterface;
use AvySearch\DTO\ExternalEntities\FileData;
use Elastic\Elasticsearch\Client;
use AvySearch\SearchInstance;

final class Upload
{
    private Client $elasticClient;

    public function __construct(SearchInstance $si)
    {
        $this->elasticClient = $si->getElasticClient();
    }

    /**
     * Загружает информацию о файле в индексы для дальнейшего поиска.
     * Должны загружаться только уникальные файлы, так как в противном
     * случае будут дубликаты в поисковых ответах.
     * Также не должны дублироваться категории у файла
     *
     * @throws \Exception
     */
    public function document(FileData $fileData)
    {
        $request = (new UploadFile())
            ->setCustomId($fileData->getCustomId())
            ->setFilename($fileData->getFilename())
            ->setOriginFilename($fileData->getOriginFilename())
            ->setByteSize($fileData->getByteSize())
            ->setLang($fileData->getLanguageAlias())
            ->setCatalogText($fileData->getText())
            ->setFileType($fileData->getFileTypeName())
            ->setCategories($fileData->getCategories())
            ->setCategoriesForGrouping($fileData->getCategoriesForGrouping());

        $request->execSync($this->elasticClient);
    }

    /**
     * Загружает информацию о подсказке в индекс для дальнейшего поиска.
     * Подсказки внутри каждого типа должны быть уникальны, иначе в поисковой
     * выдачи будут дубликаты.
     *
     * @param AttributeInterface $attr - атрибут
     *
     */
    public function UnboundAttribute(AttributeInterface $attr): void
    {
        $request = (new UploadSuggest())
            ->setAttribute($attr);

        $request->execSync($this->elasticClient);
    }

}
