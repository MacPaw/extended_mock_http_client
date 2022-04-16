<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Functional\Builder\HttpFixtureBuilder;

use ExtendedMockHttpClient\Excpetion\Builder\HttpFixtureBuilder\InvalidMethodArgumentException;

class BodyTest extends AbstractHttpFixtureBuilderTest
{
    public function testString(): void
    {
        $result = $this->getHttpFixtureBuilder()->body('test');

        self::assertCount(1, $result['body']);

        $requestItem =  array_shift($result['body']);
        self::assertTrue($requestItem->__invoke('test'));
        self::assertFalse($requestItem->__invoke('t'));
        self::assertFalse($requestItem->__invoke('test123'));
    }

    public function testArray(): void
    {
        $result = $this->getHttpFixtureBuilder()->body(['test1', 'test2']);

        self::assertCount(1, $result['body']);

        $requestItem =  array_shift($result['body']);
        self::assertTrue($requestItem->__invoke('test1'));
        self::assertTrue($requestItem->__invoke('test2'));
        self::assertFalse($requestItem->__invoke('test3'));
    }

    public function testNestedRequestItem(): void
    {
        $result = $this->getHttpFixtureBuilder()->body(
            $this->getHttpFixtureBuilder()->stringRegex('/test/')
        );

        self::assertCount(1, $result['body']);

        $requestItem =  array_shift($result['body']);
        self::assertTrue($requestItem->__invoke('test1'));
        self::assertFalse($requestItem->__invoke('foobarbaz'));
    }

    public function testFail(): void
    {
        $this->expectException(InvalidMethodArgumentException::class);

        $this->getHttpFixtureBuilder()->body(false);
    }
}
