<?php

namespace ExtendedMockHttpClient\Model;

use ExtendedMockHttpClient\Builder\RequestMockBuilder;
use ExtendedMockHttpClient\Comparators\ComparatorInterface;

class RequestMock
{
    /** @var ComparatorInterface[] */
    private $methodComparators;
    /** @var ComparatorInterface[] */
    private $urlComparators;
    /** @var ComparatorInterface[] */
    private $queryComparators;

    public function __construct(RequestMockBuilder $builder)
    {
        $this->methodComparators = $builder->getMethodComparators();
        $this->urlComparators = $builder->getUrlComparators();
        $this->queryComparators = $builder->getQueryComparators();
    }

    public function isSuitable(string $method, string $url): bool
    {
        foreach ($this->methodComparators as $comparator) {
            if (!$comparator->isMatch($method)) {
                return false;
            }
        }

        foreach ($this->urlComparators as $comparator) {
            if (!$comparator->isMatch($url)) {
                return false;
            }
        }

        foreach ($this->queryComparators as $comparator) {
            if (!$comparator->isMatch($url)) {
                return false;
            }
        }

        return true;
    }
}
