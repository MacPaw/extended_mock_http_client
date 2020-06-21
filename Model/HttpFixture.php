<?php

namespace App\Tests\ExtendedMockHttpClient;

use Symfony\Contracts\HttpClient\ResponseInterface;

class HttpFixture
{
    /**
     * @var RequestMock
     */
    private $request;

    /**
     * @var ResponseInterface
     */
    private $response;

    public function __construct(RequestMock $request, ResponseInterface $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function isSuitable(string $method, string $url): bool
    {
        return $this->request->isSuitable($method, $url);
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
