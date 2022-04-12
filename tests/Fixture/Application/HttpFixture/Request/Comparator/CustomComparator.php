<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Fixture\Application\HttpFixture\Request\Comparator;

use ExtendedMockHttpClient\HttpFixture\Request\Comparator\ComparatorInterface;

class CustomComparator implements ComparatorInterface
{
    /**
     * @var string
     */
    private $stringPart1;

    /**
     * @var string
     */
    private $stringPart2;

    public static function getName(): string
    {
        return 'custom';
    }

    public function __construct(string $stringPart1, string $stringPart2)
    {
        $this->stringPart1 = $stringPart1;
        $this->stringPart2 = $stringPart2;
    }

    public function __invoke($value): bool
    {
        return $value === "$this->stringPart1.$this->stringPart2";
    }
}
