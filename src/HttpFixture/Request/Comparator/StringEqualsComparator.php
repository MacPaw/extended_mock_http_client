<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\HttpFixture\Request\Comparator;

class StringEqualsComparator implements ComparatorInterface
{
    /**
     * @var string
     */
    private $value;

    public static function getName(): string
    {
        return 'stringEquals';
    }

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function __invoke($value): bool
    {
        return $this->value === $value;
    }
}
