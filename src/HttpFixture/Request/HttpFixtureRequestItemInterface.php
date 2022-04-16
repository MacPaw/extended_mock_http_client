<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\HttpFixture\Request;

interface HttpFixtureRequestItemInterface
{
    public static function getName(): string;

    /**
     * @param mixed $value
     */
    public function __invoke($value): bool;
}
