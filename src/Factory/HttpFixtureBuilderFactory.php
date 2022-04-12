<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Factory;

use ExtendedMockHttpClient\Builder\HttpFixtureBuilder;

class HttpFixtureBuilderFactory
{
    /**
     * @var HttpFixtureFactory
     */
    protected $httpFixtureFactory;

    public function __construct(HttpFixtureFactory $httpFixtureFactory)
    {
        $this->httpFixtureFactory = $httpFixtureFactory;
    }

    public function create(): HttpFixtureBuilder
    {
        return new HttpFixtureBuilder($this->httpFixtureFactory);
    }
}
