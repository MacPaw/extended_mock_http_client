<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Builder;

use ExtendedMockHttpClient\Comparators\ArrayHasValueByKeyComparator;
use ExtendedMockHttpClient\Comparators\ComparatorInterface;
use ExtendedMockHttpClient\Comparators\QueryComparator;
use ExtendedMockHttpClient\Comparators\StringEqualsComparator;
use ExtendedMockHttpClient\Comparators\UrlComparator;
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
        return $this->addMethodComparator(new StringEqualsComparator($method));
    }

    public function urlEquals(string $url): self
    {
        return $this->addUrlComparator(new UrlComparator([new StringEqualsComparator($url)]));
    }

    public function queryShouldContain(string $key, string $value): self
    {
        return $this->addQueryComparator(new QueryComparator([new ArrayHasValueByKeyComparator($key, $value)]));
    }

    public function bodyEquals(string $body): self
    {
        return $this->addBodyComparator(new StringEqualsComparator($body));
    }

    public function addMethodComparator(ComparatorInterface $comparator): self
    {
        $this->methodComparators[] = $comparator;

        return $this;
    }

    public function addUrlComparator(ComparatorInterface $comparator): self
    {
        $this->urlComparators[] = $comparator;

        return $this;
    }

    public function addQueryComparator(ComparatorInterface $comparator): self
    {
        $this->queryComparators[] = $comparator;

        return $this;
    }

    public function addBodyComparator(ComparatorInterface $comparator): self
    {
        $this->bodyComparators[] = $comparator;

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
