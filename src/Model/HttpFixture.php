<?php

namespace ExtendedMockHttpClient\Model;

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

    public function isSuitable(string $method, string $url, string $body): bool
    {
        return $this->request->isSuitable($method, $url, $body);
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
