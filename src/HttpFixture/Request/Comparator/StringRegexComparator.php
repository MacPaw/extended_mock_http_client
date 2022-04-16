<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\HttpFixture\Request\Comparator;

class StringRegexComparator implements ComparatorInterface
{
    /**
     * @var string
     */
    private $regex;

    public static function getName(): string
    {
        return 'stringRegex';
    }

    public function __construct(string $regex)
    {
        $this->regex = $regex;
    }

    public function __invoke($value): bool
    {
        return is_string($value) && preg_match($this->regex, $value) === 1;
    }
}
