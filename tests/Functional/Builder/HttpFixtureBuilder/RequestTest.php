<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Functional\Builder\HttpFixtureBuilder;

use ExtendedMockHttpClient\Excpetion\Builder\HttpFixtureBuilder\InvalidRequestKeyException;
use ExtendedMockHttpClient\HttpFixture\Request\Comparator\StringEqualsComparator;

class RequestTest extends AbstractHttpFixtureBuilderTest
{
    public function testEmpty(): void
    {
        $this->getHttpFixtureBuilder()->request();

        $this->expectNotToPerformAssertions();
    }

    public function testSuccess(): void
    {
        $this->getHttpFixtureBuilder()->request(
            $this->getHttpFixtureBuilder()->method('GET'),
            $this->getHttpFixtureBuilder()->body('test')
        );

        $this->expectNotToPerformAssertions();
    }

    public function testSuccessArray(): void
    {
        $this->getHttpFixtureBuilder()->request(
            ['method' => new StringEqualsComparator('GET')],
            ['body' => new StringEqualsComparator('test')]
        );

        $this->expectNotToPerformAssertions();
    }

    public function testFail(): void
    {
        $this->expectException(InvalidRequestKeyException::class);

        $this->getHttpFixtureBuilder()->request(
            ['invalid' => new StringEqualsComparator('GET')]
        );
    }
}
