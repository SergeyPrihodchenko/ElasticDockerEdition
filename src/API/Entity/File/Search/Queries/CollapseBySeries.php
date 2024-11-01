<?php

declare(strict_types=1);

namespace AvySearch\API\Entity\File\Search\Queries;

use Elastic\Elasticsearch\Client;
use AvySearch\API\Entity\BaseSearchQuery;
use AvySearch\API\Entity\File\Search\Mappers\SeriesCollapsedMapper;
use Http\Promise\Promise;

final class CollapseBySeries extends BaseSearchQuery
{
    private Promise $promise;

    private array $fields = [
        'custom-id',
        'file-size',
        'file-name',
        'origin-file-name',
        'series',
        'lang'
    ];
    private string $searchText;
    private array $seriesIds = [];
    private int $seriesSize;
    private int $from = 0;

    public function setSearchText(string $searchText): self
    {
        $this->searchText = $searchText;

        return $this;
    }

    public function setSeriesSize(int $seriesSize): self
    {
        $this->seriesSize = $seriesSize;

        return $this;
    }

    public function setSeriesIds(array $seriesIds): self
    {
        $this->seriesIds = $seriesIds;

        return $this;
    }

    public function setFromRecordNumber(int $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function sendAsync(Client $elasticClient): Promise
    {
        $query = [
            'index' => 'files-seria-_index_alias',
            'ignore_unavailable' => true,
            'body' => [
                '_source' => false,
                'from' => $this->from,
                'size' => $this->seriesSize,
                'query' => [
                    "bool" => [
                        "must" => [
                            "multi_match" => [
                                "query" => $this->searchText,
                                "fields" => [
                                    "categories-full-text^3",
                                    "text-content"
                                ],
                                "minimum_should_match" => "80%"
                            ]
                        ],
                        "should" => [
                            "match_phrase" => [
                                "text-content" => [
                                    "query" => $this->searchText,
                                    "boost" => 2
                                ]
                            ]
                        ]
                    ]
                ],
                'collapse' => [
                    'field' => 'series',
                    'inner_hits' => [
                        '_source' => false,
                        'fields' => $this->fields,
                        'name' => 'file-name',
                        'size' => $this->STD_INNER_HITS_SIZE,
                        'highlight' => [
                            'fields' => [
                                'text-content' => [
                                    'pre_tags' => $this->HIGHLIGHT_PRE_TAG,
                                    'post_tags' => $this->HIGHLIGHT_POST_TAG
                                ],
                                "categories-full-text" => [
                                    "pre_tags" => $this->HIGHLIGHT_PRE_TAG,
                                    "post_tags" => $this->HIGHLIGHT_POST_TAG
                                ]
                            ]
                        ]
                    ],
                    'max_concurrent_group_searches' => 3
                ],
                "aggs" => [
                    "total" => [
                        "cardinality" => [
                            "field" => "series"
                        ]
                    ]
                ]
            ]
        ];

        if (!empty($this->seriesIds)) {
            $query['body']['query']['bool']['filter'] = [
                "terms" => [
                    "series" => $this->seriesIds
                ]
            ];
        }

        $this->promise = $elasticClient->search($query);

        return $this->promise;
    }

    public function await(): array
    {
        return (new SeriesCollapsedMapper())->map(
            $this->promise->wait()->asArray(),
            $this->seriesSize
        );
    }
}
