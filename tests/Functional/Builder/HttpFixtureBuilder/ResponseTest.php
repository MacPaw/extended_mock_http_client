<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Functional\Builder\HttpFixtureBuilder;

use ExtendedMockHttpClient\Excpetion\Builder\HttpFixtureBuilder\InvalidMethodArgumentException;
use ExtendedMockHttpClient\Excpetion\Builder\HttpFixtureBuilder\InvalidResponseArgumentTypeException;
use ExtendedMockHttpClient\Excpetion\Builder\HttpFixtureBuilder\InvalidResponseMethodArgumentCountException;
use Symfony\Component\HttpClient\Response\MockResponse;

class ResponseTest extends AbstractHttpFixtureBuilderTest
{
    public function testMockResponse(): void
    {
        $this->getHttpFixtureBuilder()->response(new MockResponse());

        $this->expectNotToPerformAssertions();
    }

    public function testCode(): void
    {
        $this->getHttpFixtureBuilder()->response(204);

        $this->expectNotToPerformAssertions();
    }

    public function testCodeAndBody(): void
    {
        $this->getHttpFixtureBuilder()->response(204, ' body');

        $this->expectNotToPerformAssertions();
    }

    public function testCodeAndBodyAndHeaders(): void
    {
        $this->getHttpFixtureBuilder()->response(204, ' body', [
            'x-header' => 'x-value'
        ]);

        $this->expectNotToPerformAssertions();
    }

    public function testCallback(): void
    {
        $this->getHttpFixtureBuilder()->response(function (): MockResponse {
            return new MockResponse();
        });

        $this->expectNotToPerformAssertions();
    }

    public function testFailLacksArguments(): void
    {
        $this->expectException(InvalidResponseMethodArgumentCountException::class);

        $this->getHttpFixtureBuilder()->response();
    }

    public function testFailExtraArguments(): void
    {
        $this->expectException(InvalidResponseMethodArgumentCountException::class);

        $this->getHttpFixtureBuilder()->response(204, ' body', [
            'x-header' => 'x-value'
        ], 'extra');
    }

    public function testFailFirstArgumentType(): void
    {
        $this->expectException(InvalidResponseArgumentTypeException::class);

        $this->getHttpFixtureBuilder()->response('invalid');
    }

    public function testFailSecondArgumentType(): void
    {
        $this->expectException(InvalidResponseArgumentTypeException::class);

        $this->getHttpFixtureBuilder()->response(200, 200);
    }

    public function testFailThirdArgumentType(): void
    {
        $this->expectException(InvalidResponseArgumentTypeException::class);

        $this->getHttpFixtureBuilder()->response(204, ' body', 'invalid');
    }
}
