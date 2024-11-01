<?php

declare(strict_types=1);

namespace AvySearch\API\Entity\UnboundAttributes\Search\Queries;

use AvySearch\API\Entity\BaseSearchQuery;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Exception;
use Http\Promise\Promise;
use AvySearch\API\Entity\UnboundAttributes\Search\Mappers\DefaultSearchMapper;

final class DefaultSearch extends BaseSearchQuery
{
    private Promise $promise;

    private array $fields = [
        "value",
        "value._each-token-n-gram",
        "value._concatenated-prefix"
    ];
    private string $searchText;

    public function setSearchText(string $searchText): self
    {
        $this->searchText = $searchText;

        return $this;
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function sendAsync(Client $elasticClient): Promise
    {
        $this->promise = $elasticClient->search([
            'index' => 'unbound-attributes_alias',
            'body' => [
                '_source' => false,
                'query' => [
                    'multi_match' => [
                        'query' => $this->searchText,
                        'fields' => $this->fields
                    ],
                ],
                'collapse' => [
                    'field' => 'type',
                    'inner_hits' => [
                        '_source' => true,
                        'fields' => ['value'],
                        'name' => 'value',
                        'size' => $this->STD_INNER_HITS_SIZE,
                        'highlight' => [
                            'type' => 'fvh',
                            'fields' => [
                                'value*' => [
                                    'pre_tags' => $this->HIGHLIGHT_PRE_TAG,
                                    'post_tags' => $this->HIGHLIGHT_POST_TAG
                                ],
                            ]
                        ]
                    ],
                    'max_concurrent_group_searches' => 4,
                ],
            ],
        ]);

        return $this->promise;
    }

    /**
     * @throws Exception
     */
    public function await(): array
    {
        return (new DefaultSearchMapper())->map($this->promise->wait()->asArray());
    }
}
