<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Comparators;

class AndComparator implements ComparatorInterface
{
    /**
     * @var ComparatorInterface[]
     */
    protected $comparators;

    /**
     * @param ComparatorInterface|ComparatorInterface[] $comparators
     */
    public function __construct($comparators)
    {
        if ($comparators instanceof ComparatorInterface) {
            $comparators = [$comparators];
        }

        foreach ($comparators as $comparator) {
            $this->addComparator($comparator);
        }
    }

    private function addComparator(ComparatorInterface $comparator): void
    {
        $this->comparators[] = $comparator;
    }

    public function isMatch($value): bool
    {
        foreach ($this->comparators as $comparator) {
            if (!$comparator->isMatch($value)) {
                return false;
            }
        }

        return true;
    }
}
