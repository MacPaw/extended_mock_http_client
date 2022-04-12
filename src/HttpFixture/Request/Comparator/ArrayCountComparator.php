<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\HttpFixture\Request\Comparator;

class ArrayCountComparator implements ComparatorInterface
{
    /**
     * @var int
     */
    private $count;

    public static function getName(): string
    {
        return 'arrayCount';
    }

    public function __construct(int $count)
    {
        $this->count = $count;
    }

    public function __invoke($value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        return count($value) === $this->count;
    }
}
