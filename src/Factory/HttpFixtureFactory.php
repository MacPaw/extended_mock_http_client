<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Factory;

use ExtendedMockHttpClient\Excpetion\Factory\HttpFixtureFactory\NotFoundKeyInAllowedNestedKeysException;
use ExtendedMockHttpClient\Excpetion\Factory\HttpFixtureFactory\RequiredKeyNotFoundException;
use ExtendedMockHttpClient\HttpFixture\HttpFixture;
use ExtendedMockHttpClient\HttpFixture\MockRequest;
use ExtendedMockHttpClient\HttpFixture\Request\AbstractNestedHttpFixtureRequestItem;
use ExtendedMockHttpClient\HttpFixture\Request\HttpFixtureRequestItemInterface;

class HttpFixtureFactory
{
    /**
     * @var array<string, string[]>
     */
    private $allowedNestedKeys;

    public function __construct(array $allowedNestedKeys)
    {
        foreach ($allowedNestedKeys as $key => $nestedKeys) {
            foreach ($nestedKeys as $nestedKey) {
                $this->add($key, $nestedKey);
            }
        }
    }

    public function add(string $key, string $nestedKey): void
    {
        if (!isset($this->allowedNestedKeys[$key])) {
            $this->allowedNestedKeys[$key] = [];
        }

        $this->allowedNestedKeys[$key][] = $nestedKey;
    }

    public function createFromArray(array $parameters): HttpFixture
    {
        if (!array_key_exists('request', $parameters)) {
            throw new RequiredKeyNotFoundException('request');
        }

        if (!array_key_exists('response', $parameters)) {
            throw new RequiredKeyNotFoundException('response');
        }

        $this->validateRequestParametersRecursive(['request' => $parameters['request']]);

        $mockRequest = new MockRequest($parameters['request']);
        $mockResponse = $parameters['response'];

        return new HttpFixture($mockRequest, $mockResponse);
    }

    /**
     * @param array<mixed>|HttpFixtureRequestItemInterface $parameters
     */
    private function validateRequestParametersRecursive($parameters, string $previousKey = ''): void
    {
        foreach ($parameters as $key => $value) {
            if (!is_string($key) && $value instanceof HttpFixtureRequestItemInterface) {
                $key = $value::getName();
            }

            if (!in_array($key, $this->allowedNestedKeys[$previousKey], true)) {
                throw new NotFoundKeyInAllowedNestedKeysException($previousKey);
            }

            if (is_array($value)) {
                $this->validateRequestParametersRecursive($value, $key);
            } elseif ($value instanceof AbstractNestedHttpFixtureRequestItem) {
                $this->validateRequestParametersRecursive($value->getItems(), $key);
            }
        }
    }
}
