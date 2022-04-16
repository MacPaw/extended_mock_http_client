<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Functional\Builder\HttpFixtureBuilder;

use ExtendedMockHttpClient\Excpetion\Builder\HttpFixtureBuilder\InvalidMethodArgumentException;

class MethodTest extends AbstractHttpFixtureBuilderTest
{
    public function testString(): void
    {
        $result = $this->getHttpFixtureBuilder()->method('GET');

        self::assertCount(1, $result['method']);

        $requestItem =  array_shift($result['method']);
        self::assertTrue($requestItem->__invoke('GET'));
        self::assertFalse($requestItem->__invoke('POST'));
    }

    public function testArray(): void
    {
        $result = $this->getHttpFixtureBuilder()->method(['GET', 'POST']);

        self::assertCount(1, $result['method']);

        $requestItem =  array_shift($result['method']);
        self::assertTrue($requestItem->__invoke('GET'));
        self::assertTrue($requestItem->__invoke('POST'));
        self::assertFalse($requestItem->__invoke('PUT'));
    }

    public function testNestedRequestItem(): void
    {
        $result = $this->getHttpFixtureBuilder()->method(
            $this->getHttpFixtureBuilder()->stringRegex('/GET/')
        );

        self::assertCount(1, $result['method']);

        $requestItem =  array_shift($result['method']);
        self::assertTrue($requestItem->__invoke('GET'));
        self::assertFalse($requestItem->__invoke('POST'));
    }

    public function testFail(): void
    {
        $this->expectException(InvalidMethodArgumentException::class);

        $this->getHttpFixtureBuilder()->method(false);
    }
}
