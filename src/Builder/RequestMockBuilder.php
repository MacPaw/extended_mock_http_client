<?php

namespace ExtendedMockHttpClient\Builder;

use ExtendedMockHttpClient\Comparators\ComparatorInterface;
use ExtendedMockHttpClient\Comparators\Query\QueryShouldContain;
use ExtendedMockHttpClient\Comparators\Url\StringEquals as UrlStringEquals;
use ExtendedMockHttpClient\Comparators\StringEquals;
use ExtendedMockHttpClient\Model\RequestMock;

class RequestMockBuilder
{
    /** @var ComparatorInterface[] */
    private $methodComparators = [];
    /** @var ComparatorInterface[] */
    private $urlComparators = [];
    /** @var ComparatorInterface[] */
    private $queryComparators = [];
    /** @var ComparatorInterface[] */
    private $bodyComparators = [];

    public function methodEquals(string $method): self
    {
        $this->methodComparators[] = new StringEquals($method);

        return $this;
    }

    public function urlEquals(string $url): self
    {
        $this->urlComparators[] = new UrlStringEquals($url);

        return $this;
    }

    public function queryShouldContain(string $fieldName, string $value): self
    {
        $this->queryComparators[] = new QueryShouldContain($fieldName, $value);

        return $this;
    }

    public function bodyEquals(string $body): self
    {
        $this->bodyComparators[] = new StringEquals($body);

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

    /**
     * @return ComparatorInterface[]
     */
    public function getQueryComparators(): array
    {
        return $this->queryComparators;
    }

    /**
     * @return ComparatorInterface[]
     */
    public function getBodyComparators(): array
    {
        return $this->bodyComparators;
    }

    public function build(): RequestMock
    {
        return new RequestMock($this);
    }
}
