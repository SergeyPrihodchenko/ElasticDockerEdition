<?php

declare(strict_types=1);

namespace AvySearch\API\Entity\File\Search\Mappers;

final class SeriesCollapsedMapper
{
    /**
     * @return array{
     *    items: array<int, array<int<0, max>, array{
     *      customId: string|null,
     *      categoryId: int,
     *      langAlias: string,
     *      byteSize: int,
     *      fileName: string,
     *      originName: string,
     *      suggestText: string
     *    }>>,
     *    totalHits: int,
     *    maxPage: int,
     *    pageSize: int
     * }
     */
    public function map(array $elasticResponse, int $pageSize): array
    {
        if(!isset($elasticResponse['aggregations']) || empty($elasticResponse['hits']['hits'])){
            return [
                'items' => [],
                'totalHits' => 0,
                'maxPage' => 1,
                'pageSize' => $pageSize,
            ];
        }

        $items = [];
        $total = $elasticResponse['aggregations']['total']['value'];

        foreach ($elasticResponse['hits']['hits'] as $series) {
            $item = [];
            $series_id = (int)$series['fields']['series'][0];

            foreach ($series['inner_hits']['file-name']['hits']['hits'] as $inner_item) {
                $inner_fields = $inner_item['fields'];
                $suggestText = isset($inner_item['highlight']['text-content']) ? implode("\n", $inner_item['highlight']['text-content']) : '';

                $hit = [
                    'customId' => isset($inner_fields['custom-id'][0]) ? (string)$inner_fields['custom-id'][0] : null,
                    'categoryId' => $series_id,
                    'langAlias' => (string)$inner_fields['lang'][0],
                    'byteSize' => (int)$inner_fields['file-size'][0],
                    'fileName' => (string)$inner_fields['file-name'][0],
                    'originName' => (string)$inner_fields['origin-file-name'][0],
                    'suggestText' => (string)$suggestText,
                ];

                $item[] = $hit;
            }

            $items[$series_id] = $item;
        }

        $maxPage = 0;
        if (0 !== $total) {
            $maxPage = (int)ceil($total / $pageSize);
        }

        return [
            'items' => $items,
            'totalHits' => $total,
            'maxPage' => $maxPage,
            'pageSize' => $pageSize,
        ];
    }
}
