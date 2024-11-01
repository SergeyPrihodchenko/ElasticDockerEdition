<?php

declare(strict_types=1);

namespace AvySearch\Tests;

use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        \DG\BypassFinals::enable();
    }
}
