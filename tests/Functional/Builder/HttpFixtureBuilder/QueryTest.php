<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Functional\Builder\HttpFixtureBuilder;

use ExtendedMockHttpClient\Excpetion\Builder\HttpFixtureBuilder\InvalidMethodArgumentException;

class QueryTest extends AbstractHttpFixtureBuilderTest
{
    public function testString(): void
    {
        $result = $this->getHttpFixtureBuilder()->query('foo=bar&baz=123');

        self::assertCount(1, $result['query']);

        $requestItem =  array_shift($result['query']);
        self::assertTrue($requestItem->__invoke('foo=bar&baz=123'));
        self::assertFalse($requestItem->__invoke('foo=bar'));
        self::assertFalse($requestItem->__invoke('foo=bar&baz=123&baz=123'));
    }

    public function testArray(): void
    {
        $result = $this->getHttpFixtureBuilder()->query([
            'foo' => 'bar',
            'baz' => '123'
        ]);

        self::assertCount(1, $result['query']);

        $requestItem =  array_shift($result['query']);
        self::assertTrue($requestItem->__invoke('foo=bar&baz=123'));
        self::assertFalse($requestItem->__invoke('foo=bar'));
        self::assertTrue($requestItem->__invoke('foo=bar&baz=123&baz=123'));
    }

    public function testNestedRequestItem(): void
    {
        $result = $this->getHttpFixtureBuilder()->query(
            $this->getHttpFixtureBuilder()->stringRegex('/foo/')
        );

        self::assertCount(1, $result['query']);

        $requestItem =  array_shift($result['query']);
        self::assertTrue($requestItem->__invoke('foo=bar&baz=123'));
        self::assertFalse($requestItem->__invoke('baz=123'));
    }

    public function testFail(): void
    {
        $this->expectException(InvalidMethodArgumentException::class);

        $this->getHttpFixtureBuilder()->query(false);
    }
}
