<?php

declare(strict_types=1);

namespace AvySearch\API\Entity\File\Search\Mappers;

final class DefaultMapper
{
    /**
     * @return array<int<0, max>, array{
     *      customId: string|null,
     *      langAlias: string,
     *      byteSize: int,
     *      fileName: string,
     *      originName: string,
     *      suggestText: string
     * }>
     */
    public function map(array $elasticResponse): array
    {
        $items = [];

        foreach ($elasticResponse['hits']['hits'] as $hit) {

            $suggestText = isset($hit['highlight']['text-content'])
                ? implode("\n", $hit['highlight']['text-content'])
                : '';

            $items[] = [
                'customId' => isset($hit['fields']['custom-id'][0]) ? (string)$hit['fields']['custom-id'][0] : null,
                'langAlias' => $hit['fields']['lang'][0],
                'byteSize' => $hit['fields']['file-size'][0],
                'fileName' => $hit['fields']['file-name'][0],
                'originName' => (string)$hit['fields']['origin-file-name'][0],
                'suggestText' => (string)$suggestText,
            ];
        }

        return $items;
    }
}
