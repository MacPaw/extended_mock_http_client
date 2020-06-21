<?php

namespace ExtendedMockHttpClient\Builder;

use App\Tests\ExtendedMockHttpClient\RequestMock;
use ExtendedMockHttpClient\Comparators\ComparatorInterface;
use ExtendedMockHttpClient\Comparators\StringEquals;

class RequestMockBuilder
{
    /**
     * @var ComparatorInterface[]
     */
    private $methodComparators = [];

    /**
     * @var ComparatorInterface[]
     */
    private $urlComparators = [];

    public function methodEquals(string $method): self
    {
        $this->methodComparators[] = new StringEquals($method);

        return $this;
    }

    public function urlEquals(string $url): self
    {
        $this->urlComparators[] = new StringEquals($url);

        return $this;
    }

    /**
     * @return ComparatorInterface[]
     */
    public function getMethodComparators(): array
    {
        return $this->methodComparators;
    }

    /**
     * @return ComparatorInterface[]
     */
    public function getUrlComparators(): array
    {
        return $this->urlComparators;
    }

    public function build(): RequestMock
    {
        return new RequestMock($this);
    }
}
