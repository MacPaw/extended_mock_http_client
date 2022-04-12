<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\HttpFixture\Request\Comparator;

class InArrayComparator implements ComparatorInterface
{
    /**
     * @var array
     */
    private $value;

    public static function getName(): string
    {
        return 'inArray';
    }

    public function __construct(array $value)
    {
        $this->value = $value;
    }

    public function __invoke($value): bool
    {
        return in_array($value, $this->value, true);
    }
}
