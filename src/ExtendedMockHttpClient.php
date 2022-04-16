<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient;

use ExtendedMockHttpClient\Builder\HttpFixtureBuilder;
use ExtendedMockHttpClient\Collection\FixtureCollection;
use ExtendedMockHttpClient\Excpetion\NotFountSuitableFixtureException;
use ExtendedMockHttpClient\Factory\HttpFixtureBuilderFactory;
use ExtendedMockHttpClient\HttpFixture\HttpFixture;
use Symfony\Component\Cache\ResettableInterface;
use Symfony\Component\HttpClient\HttpClientTrait;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\Response\ResponseStream;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;
use TypeError;

class ExtendedMockHttpClient implements HttpClientInterface, ResettableInterface
{
    use HttpClientTrait;

    /**
     * @var FixtureCollection
     */
    private $fixtureCollection;

    /**
     * @var string
     */
    private $baseUri;

    /**
     * @var HttpFixtureBuilderFactory
     */
    private $httpFixtureBuilderFactory;

    public function __construct(string $baseUri, HttpFixtureBuilderFactory $httpFixtureBuilderFactory)
    {
        $this->httpFixtureBuilderFactory = $httpFixtureBuilderFactory;
        $this->fixtureCollection = new FixtureCollection();
        $this->baseUri = $baseUri;
    }

    public function getHttpFixtureBuilder(): HttpFixtureBuilder
    {
        return $this->httpFixtureBuilderFactory->create();
    }

    public function createFixture(
        string $method = null,
        string $url = null,
        string $body = null,
        array $headers = null,
        int $responseCode = null,
        string $responseBody = null
    ): HttpFixture {
        $builder = $this->httpFixtureBuilderFactory->create();

        if (is_string($method)) {
            $builder->request($builder->method($method));
        }

        if (is_string($url)) {
            $query = parse_url($url, PHP_URL_QUERY);
            if (is_string($query)) {
                $builder->request($builder->url($url));
            }

            [$url] = explode('?', $url);
            $builder->request($builder->url($url));
        }

        if (is_string($body)) {
            $builder->request($builder->body($body));
        }

        if (is_array($headers)) {
            $builder->request($builder->headers($headers));
        }

        $builder->response($responseCode ?? 200, $responseBody ?? '');

        return $builder->build();
    }

    public function addFixture(HttpFixture $fixture): void
    {
        $this->fixtureCollection->addFixture($fixture);
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $request = self::prepareRequest($method, $url, $options, ['base_uri' => $this->baseUri], true);
        $url = implode('', $request[0]);
        $query = parse_url($url, PHP_URL_QUERY) ?? '';
        [$url] = explode('?', $url);
        $options = $request[1];
        $body = $options['body'] ?? '';
        $headers = array_map(static function ($value): string {
            $value = explode(': ', (string) array_pop($value));

            return implode('', array_slice($value, count($value) > 1 ? 1 : 0));
        }, $options['normalized_headers'] ?? []);

        $fixture = $this->fixtureCollection->findSuitableFixture($method, $url, $query, $body, $headers);

        if ($fixture === null) {
            throw NotFountSuitableFixtureException::fromRequestParameters($method, $url, $query, $body, $headers);
        }

        return MockResponse::fromRequest(
            $method,
            $url,
            $options,
            $fixture->getResponse($method, $url, $query, $body, $headers)
        );
    }

    public function stream($responses, float $timeout = null): ResponseStreamInterface
    {
        if ($responses instanceof ResponseInterface) {
            $responses = [$responses];
        } elseif (!is_iterable($responses)) {
            throw new TypeError(sprintf(
                '"%s()" expects parameter 1 to be an iterable of MockResponse objects, "%s" given.',
                __METHOD__,
                get_debug_type($responses)
            ));
        }

        return new ResponseStream(MockResponse::stream($responses, $timeout));
    }

    public function getBaseUri(): string
    {
        return $this->baseUri;
    }

    public function reset(): void
    {
        $this->fixtureCollection->reset();
    }
}
