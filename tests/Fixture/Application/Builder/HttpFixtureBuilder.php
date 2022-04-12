<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Fixture\Application\Builder;

use ExtendedMockHttpClient\Builder\HttpFixtureBuilder as BaseHttpFixtureBuilder;
use ExtendedMockHttpClient\Tests\Fixture\Application\HttpFixture\Request\Comparator\CustomComparator;

class HttpFixtureBuilder extends BaseHttpFixtureBuilder
{
    public function custom(string $stringPart1, string $stringPart2): CustomComparator
    {
        return new CustomComparator($stringPart1, $stringPart2);
    }
}
