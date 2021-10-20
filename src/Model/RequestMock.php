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
    /** @var ComparatorInterface[] */
    private $headersComparators;

    public function __construct(RequestMockBuilder $builder)
    {
        $this->methodComparators = $builder->getMethodComparators();
        $this->urlComparators = $builder->getUrlComparators();
        $this->queryComparators = $builder->getQueryComparators();
        $this->bodyComparators = $builder->getBodyComparators();
        $this->headersComparators = $builder->getHeadersComparators();
    }

    public function isSuitable(string $method, string $url, string $body, array $headers): bool
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

        foreach ($this->headersComparators as $comparator) {
            if (!$comparator->isMatch($headers)) {
                return false;
            }
        }

        return true;
    }
}
