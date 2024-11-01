<?php

declare(strict_types=1);

namespace AvySearch\API\Entity\File\Search\Queries;

use Elastic\Elasticsearch\Client;
use AvySearch\API\Entity\BaseSearchQuery;
use AvySearch\API\Entity\File\Search\Mappers\DefaultMapper;
use Http\Promise\Promise;

final class DefaultSearch extends BaseSearchQuery
{
    private Promise $promise;

    private array $fields = [
        'custom-id',
        'lang',
        'file-size',
        'file-name',
        'origin-file-name',
    ];
    private string $searchText;
    private int $size;
    private int $from;

    public function setSearchText(string $searchText): self
    {
        $this->searchText = $searchText;

        return $this;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function setFromRecordNumber(int $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function sendAsync(Client $elasticClient): Promise
    {
        $this->promise = $elasticClient->search([
            'index' => 'files_index_alias',
            'ignore_unavailable' => true,
            'body' => [
                '_source' => false,
                'from' => $this->from,
                'size' => $this->size,
                'fields' => $this->fields,
                'query' => [
                    "bool" => [
                        "must" => [
                            "multi_match" => [
                                "query" => $this->searchText,
                                "fields" => [
                                    "text-content"
                                ],
                                "minimum_should_match" => "80%"
                            ]
                        ],
                        "should" => [
                            "match_phrase_prefix" => [
                                "text-content" => [
                                    "query" => $this->searchText,
                                    "boost" => 100
                                ]
                            ]
                        ]
                    ]
                ],
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
            ]
        ]);

        return $this->promise;
    }

    public function await(): array
    {
        return (new DefaultMapper())->map($this->promise->wait()->asArray());
    }

}
