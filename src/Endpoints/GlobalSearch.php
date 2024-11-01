<?php

declare(strict_types=1);

namespace AvySearch\Endpoints;

use AvySearch\API\Entity\File\Search\Queries\CollapseBySeries;
use AvySearch\API\Entity\File\Search\Queries\DefaultSearch as FilesDefaultSearch;
use AvySearch\API\Entity\UnboundAttributes\Search\Queries\DefaultSearch as ProductAttrsDefaultSearch;
use AvySearch\SearchInstance;
use Elastic\Elasticsearch\Client;
use Exception;

final class GlobalSearch
{
    private Client $elasticClient;

    public function __construct(SearchInstance $si)
    {
        $this->elasticClient = $si->getElasticClient();
    }

    /**
     * Поисковые подсказки к вводимому клиентом тексту
     *
     * @param string $text - текст для поиска
     * @throws Exception
     * @phpstan-return array<string, array>
     *
     * Где ключ в массиве является типом подсказки
     * значение - массив значений подсказок у определенного типа
     */
    public function searchSuggests(string $text): array
    {
        if($text === '') {
            return [];
        }

        $pool = [];

        $pool['files'] = (new FilesDefaultSearch())
            ->setSearchText($text)
            ->setSize(3)
            ->setFromRecordNumber(0);
        $pool['attrs'] = (new ProductAttrsDefaultSearch())
            ->setSearchText($text)
            ->setInnerHitsSize(3);

        foreach($pool as $item) {
            $item->sendAsync($this->elasticClient);
        }

        $res = [];
        foreach($pool as $treadName => $item) {
            $treadResponse = $item->await();

            if($treadName === 'attrs') {
                foreach($treadResponse as $attrName => $attrValues) {
                    $res[$attrName] = $attrValues;
                }
            }

            if($treadName === 'files' && !empty($treadResponse)) {
                $res['file'] = $treadResponse;
            }
        }

        return $res;
    }

    /**
     * Поиск файлов по текстовому содержимому, также по тексту его категорий.
     * Поисковая выдача сгруппирована по категориям группировки (как правило - сериям), которые
     * были переданы отдельно при загрузке файлов
     *
     * @param string $text - текст для поиска
     * @param int[] $series - массив серий (поиск производится только по этим сериям)
     * @param int $page - номер страницы
     *
     * @phpstan-return array{
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
     *    currentPage: int,
     *    maxPage: int,
     *    pageSize: int
     * }
     */
    public function searchSeriesCollapsed(string $text, array $series = [], int $page = 1): array
    {
        $series_size = 3;
        $from = ($page - 1) * $series_size;

        $elasticQuery = (new CollapseBySeries())
            ->setSearchText($text)
            ->setSeriesSize($series_size)
            ->setSeriesIds($series)
            ->setFromRecordNumber($from);

        $elasticQuery->sendAsync($this->elasticClient);
        $res = $elasticQuery->await();

        $res['currentPage'] = $page;

        return $res;
    }

}
