<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Functional\Builder\HttpFixtureBuilder;

use ExtendedMockHttpClient\Excpetion\Builder\HttpFixtureBuilder\InvalidMethodArgumentException;

class UrlTest extends AbstractHttpFixtureBuilderTest
{
    public function testString(): void
    {
        $result = $this->getHttpFixtureBuilder()->url('https://test.test/foo');

        self::assertCount(1, $result['url']);

        $requestItem =  array_shift($result['url']);
        self::assertTrue($requestItem->__invoke('https://test.test/foo'));
        self::assertFalse($requestItem->__invoke('https://test.test/foo/bar'));
        self::assertFalse($requestItem->__invoke('https://test.test'));
    }

    public function testArray(): void
    {
        $result = $this->getHttpFixtureBuilder()->url(['https://test.test/foo', 'https://test.test/bar']);

        self::assertCount(1, $result['url']);

        $requestItem =  array_shift($result['url']);
        self::assertTrue($requestItem->__invoke('https://test.test/foo'));
        self::assertTrue($requestItem->__invoke('https://test.test/bar'));
        self::assertFalse($requestItem->__invoke('https://test.test'));
        self::assertFalse($requestItem->__invoke('https://test.test/baz'));
    }

    public function testNestedRequestItem(): void
    {
        $result = $this->getHttpFixtureBuilder()->url(
            $this->getHttpFixtureBuilder()->stringRegex('/foo/')
        );

        self::assertCount(1, $result['url']);

        $requestItem =  array_shift($result['url']);
        self::assertTrue($requestItem->__invoke('https://test.test/foo'));
        self::assertFalse($requestItem->__invoke('https://test.test/bar'));
    }

    public function testFail(): void
    {
        $this->expectException(InvalidMethodArgumentException::class);

        $this->getHttpFixtureBuilder()->url(false);
    }
}
