<?php

declare(strict_types=1);

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
    /** @var ComparatorInterface[] */
    private $bodyComparators;

    public function __construct(RequestMockBuilder $builder)
    {
        $this->methodComparators = $builder->getMethodComparators();
        $this->urlComparators = $builder->getUrlComparators();
        $this->queryComparators = $builder->getQueryComparators();
        $this->bodyComparators = $builder->getBodyComparators();
    }

    public function isSuitable(string $method, string $url, string $body): bool
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

        foreach ($this->bodyComparators as $comparator) {
            if (!$comparator->isMatch($body)) {
                return false;
            }
        }

        return true;
    }
}
