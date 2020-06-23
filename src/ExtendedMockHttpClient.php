<?php

namespace ExtendedMockHttpClient;

use Exception;
use ExtendedMockHttpClient\Collection\FixtureCollection;
use ExtendedMockHttpClient\Model\HttpFixture;
use Iterator;
use Symfony\Component\HttpClient\HttpClientTrait;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\Response\ResponseStream;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;
use TypeError;

class ExtendedMockHttpClient implements HttpClientInterface
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
     * @param string $baseUri
     * @param HttpFixture|HttpFixture[]|iterable|null $fixtures
     */
    public function __construct(string $baseUri, $fixtures = null)
    {
        $this->fixtureCollection = new FixtureCollection();

        if ($fixtures instanceof HttpFixture) {
            $fixtures = [$fixtures];
        }

        if (!$fixtures instanceof Iterator && null !== $fixtures) {
            foreach ($fixtures as $fixture) {
                $this->addFixture($fixture);
            }
        }

        $this->baseUri = $baseUri;
    }

    public function addFixture(HttpFixture $fixture)
    {
        $this->fixtureCollection->addFixture($fixture);
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $request = self::prepareRequest($method, $url, $options, ['base_uri' => $this->baseUri], true);
        $url = implode('', $request[0]);
        $options = $request[1];

        $fixture = $this->fixtureCollection->findSuitableFixture($method, $url, $options['body']);

        if ($fixture === null) {
            throw new Exception('Not found suitable fixture');
        }

        return MockResponse::fromRequest($method, $url, $options, $fixture->getResponse());
    }

    public function stream($responses, float $timeout = null): ResponseStreamInterface
    {
        if ($responses instanceof ResponseInterface) {
            $responses = [$responses];
        } elseif (!is_iterable($responses)) {
            throw new TypeError(sprintf('"%s()" expects parameter 1 to be an iterable of MockResponse objects, "%s" given.', __METHOD__, get_debug_type($responses)));
        }

        return new ResponseStream(MockResponse::stream($responses, $timeout));
    }
}
