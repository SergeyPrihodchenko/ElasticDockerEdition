<?php

declare(strict_types=1);

namespace AvySearch\API\Entity\UnboundAttributes\Search\Mappers;

final class DefaultSearchMapper
{
    /**
     * @return array<string, array>
     * 
     * ключ - ключ в массиве, являющийся типом подсказки
     * mixed[] - массив значений подсказок у определенного типа
     */
    public function map(array $elasticResponse): array
    {
        $result = [];

        foreach ($elasticResponse['hits']['hits'] as $hit) {
            
            $inner_hits = [];
            $type = $hit['fields']['type'][0];

            foreach ($hit['inner_hits']['value']['hits']['hits'] as $inner_hit) {
                $h = $inner_hit['_source'];
                $h['value_highlighted'] = '';
                
                if (isset($inner_hit['highlight']['value._concatenated-prefix'][0])) {
                    $h['value_highlighted'] = $inner_hit['highlight']['value._concatenated-prefix'][0];
                    
                } elseif (isset($inner_hit['highlight']['value._each-token-n-gram'][0])) {
                    $h['value_highlighted'] = $inner_hit['highlight']['value._each-token-n-gram'][0];
                    
                } elseif (isset($inner_hit['highlight']['value'][0])) {
                    $h['value_highlighted'] = $inner_hit['highlight']['value'][0];
                }
                
                $inner_hits[] = $h;
            }

            $result[$type] = $inner_hits;
        }

        return $result;
    }
}
