<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Fixture\Application\Factory;

use ExtendedMockHttpClient\Factory\HttpFixtureBuilderFactory as BaseHttpFixtureBuilderFactory;
use ExtendedMockHttpClient\Builder\HttpFixtureBuilder as BaseHttpFixtureBuilder;
use ExtendedMockHttpClient\Tests\Fixture\Application\Builder\HttpFixtureBuilder;

class HttpFixtureBuilderFactory extends BaseHttpFixtureBuilderFactory
{
    public function create(): BaseHttpFixtureBuilder
    {
        return new HttpFixtureBuilder($this->httpFixtureFactory);
    }
}
