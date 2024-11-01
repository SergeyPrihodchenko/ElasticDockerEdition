<?php

declare(strict_types=1);

namespace AvySearch\API\Entity;

use Elastic\Elasticsearch\Client;

abstract class BaseSearchQuery implements SearchQueryInterface
{
    protected string $HIGHLIGHT_PRE_TAG = '<highlight>';
    protected string $HIGHLIGHT_POST_TAG = '</highlight>';
    protected int $STD_INNER_HITS_SIZE = 5;

    public function setHighlightPreTag(string $tag): self
    {
        $this->HIGHLIGHT_PRE_TAG = $tag;

        return $this;
    }

    public function setHighlightPostTag(string $tag): self
    {
        $this->HIGHLIGHT_POST_TAG = $tag;

        return $this;
    }

    public function setInnerHitsSize(int $size): self
    {
        $this->STD_INNER_HITS_SIZE = $size;

        return $this;
    }
}
