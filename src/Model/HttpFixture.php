<?php

declare(strict_types=1);

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

    /**
     * @var int
     */
    private $calledTimes = 0;

    public function __construct(RequestMock $request, ResponseInterface $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function isSuitable(string $method, string $url, string $body): bool
    {
        $isSuitable = $this->request->isSuitable($method, $url, $body);

        if ($isSuitable) {
            ++$this->calledTimes;
        }

        return $isSuitable;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function getCalledTimes(): int
    {
        return $this->calledTimes;
    }
}
