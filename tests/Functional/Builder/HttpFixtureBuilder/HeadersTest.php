<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Functional\Builder\HttpFixtureBuilder;

use ExtendedMockHttpClient\Excpetion\Builder\HttpFixtureBuilder\InvalidMethodArgumentException;

class HeadersTest extends AbstractHttpFixtureBuilderTest
{
    public function testArray(): void
    {
        $result = $this->getHttpFixtureBuilder()->headers([
            'x-header-1' => 'x-value-1',
            'x-header-2' => 'x-value-2',
        ]);

        self::assertCount(1, $result['headers']);

        $requestItem =  array_shift($result['headers']);
        self::assertFalse($requestItem->__invoke([
            'x-header-1' => 'x-value-1',
        ]));
        self::assertTrue($requestItem->__invoke([
            'x-header-1' => 'x-value-1',
            'x-header-2' => 'x-value-2',
        ]));
        self::assertTrue($requestItem->__invoke([
            'x-header-1' => 'x-value-1',
            'x-header-2' => 'x-value-2',
            'x-header-3' => 'x-value-3',
        ]));
    }

    public function testNestedRequestItem(): void
    {
        $result = $this->getHttpFixtureBuilder()->headers(
            $this->getHttpFixtureBuilder()->arrayCount(1)
        );

        self::assertCount(1, $result['headers']);

        $requestItem =  array_shift($result['headers']);
        self::assertTrue($requestItem->__invoke([
            'x-header-1' => 'x-value-1',
        ]));
        self::assertFalse($requestItem->__invoke([
            'x-header-1' => 'x-value-1',
            'x-header-2' => 'x-value-2',
        ]));
        self::assertFalse($requestItem->__invoke([
            'x-header-1' => 'x-value-1',
            'x-header-2' => 'x-value-2',
            'x-header-3' => 'x-value-3',
        ]));
    }

    public function testFail(): void
    {
        $this->expectException(InvalidMethodArgumentException::class);

        $this->getHttpFixtureBuilder()->headers(false);
    }
}
