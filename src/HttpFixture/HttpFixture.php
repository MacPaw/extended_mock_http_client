<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\HttpFixture;

use Symfony\Component\HttpClient\Response\MockResponse;

class HttpFixture
{
    /**
     * @var MockRequest
     */
    private $request;

    /**
     * @var MockResponse|callable
     */
    private $response;

    /**
     * @var int
     */
    private $calledTimes = 0;

    /**
     * @param MockRequest           $request
     * @param MockResponse|callable $response
     */
    public function __construct(MockRequest $request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function isSuitable(string $method, string $url, string $query, string $body, array $headers): bool
    {
        $isSuitable = $this->request->isSuitable($method, $url, $query, $body, $headers);

        if ($isSuitable) {
            ++$this->calledTimes;
        }

        return $isSuitable;
    }

    public function getRequest(): MockRequest
    {
        return $this->request;
    }

    public function getResponse(string $method, string $url, string $query, string $body, array $headers): MockResponse
    {
        if (is_callable($this->response)) {
            return call_user_func($this->response, $method, $url, $query, $body, $headers);
        }

        return $this->response;
    }

    public function getCalledTimes(): int
    {
        return $this->calledTimes;
    }
}
