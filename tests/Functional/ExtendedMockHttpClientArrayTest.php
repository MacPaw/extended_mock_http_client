<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Functional;

use ExtendedMockHttpClient\Builder\RequestMockBuilder;
use ExtendedMockHttpClient\Comparators\AndComparator;
use ExtendedMockHttpClient\Comparators\ArrayComparator;
use ExtendedMockHttpClient\Comparators\JsonComparator;
use ExtendedMockHttpClient\Comparators\OrComparator;
use ExtendedMockHttpClient\Comparators\RegexComparator;
use ExtendedMockHttpClient\Comparators\StringEqualsComparator;
use ExtendedMockHttpClient\Excpetion\NotFountSuitableFixtureException;
use ExtendedMockHttpClient\ExtendedMockHttpClient;
use ExtendedMockHttpClient\Model\HttpFixture;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class ExtendedMockHttpClientArrayTest extends TestCase
{
    /**
     * @param array $actualArray
     *
     * @dataProvider fullMatchArrayDataProvider
     */
    public function testSuccessFullMatch(array $actualArray): void
    {
        $client = new ExtendedMockHttpClient('http://test.test');
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->addBodyComparator(new JsonComparator(new ArrayComparator($actualArray)))
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $response = $client->request('POST', 'http://test.test/foo/bar?qwe=rty', [
            'json' => $actualArray,
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('response body', $response->getContent(false));
    }

    /**
     * @param array $actualArray
     *
     * @dataProvider fullMatchArrayDataProvider
     */
    public function testFailFullMatch(array $actualArray): void
    {
        $array = ['definitely wrong array'];

        $client = new ExtendedMockHttpClient('http://test.test');
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->addBodyComparator(new JsonComparator(new ArrayComparator($array)))
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $this->expectException(NotFountSuitableFixtureException::class);

        $client->request('POST', 'http://test.test/foo/bar?qwe=rty', [
            'json' => $actualArray,
        ]);
    }

    public function fullMatchArrayDataProvider(): array
    {
        return [
            [[
                'test1' => 'value1',
            ]],
            [[
                'value1',
            ]],
            [[
                'test1' => 'value1',
                'test2' => 'value2',
                'test3' => 'value3',
            ]],
            [[
                'value1',
                'value2',
                'value3',
            ]],
            [[
                1,
                2,
                3,
            ]],
            [[
                'test1' => [
                    1,
                    2,
                    3
                ],
                'test2' => [
                    'test1' => 'value1',
                    'test2' => 'value2',
                    'test3' => 'value3',
                ],
                'test3' => [
                    [
                        1,
                        2,
                        3
                    ]
                ],
                'test4' => [
                    [
                        [
                            'test1' => 'value1',
                            'test2' => 'value2',
                            'test3' => 'value3',
                        ]
                    ]
                ],
            ]],
        ];
    }

    /**
     * @param array $expectedArray
     * @param array $actualArray
     *
     * @dataProvider regexKeysArrayDataProvider
     */
    public function testSuccessRegex(array $expectedArray, array $actualArray): void
    {
        $client = new ExtendedMockHttpClient('http://test.test');
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->addBodyComparator(new JsonComparator(new ArrayComparator($expectedArray)))
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $response = $client->request('POST', 'http://test.test/foo/bar?qwe=rty', [
            'json' => $actualArray,
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('response body', $response->getContent(false));
    }

    public function regexKeysArrayDataProvider(): array
    {
        return [
            [
                [
                    '/\d+/' => 'value1',
                ],
                [
                    321 => 'value1',
                ],
            ],
            [
                [
                    '/1\d+/' => 'value1',
                ],
                [
                    1321 => 'value1',
                ],
            ],
            [
                [
                    '/test\w+/' => 'value2',
                    '/test\d+/' => 'value3',
                ],
                [
                    'testFoo' => 'value2',
                    'test321' => 'value3',
                ],
            ],
            [
                [
                    [
                        '/root\w+/' => [
                            '/\d+/' => 'value1',
                            '/test\w+/' => 'value2',
                            '/test\d+/' => 'value3',
                        ]
                    ]
                ],
                [
                    [
                        'rootKey' => [
                            321 => 'value1',
                            'testFoo' => 'value2',
                            'test321' => 'value3',
                        ]
                    ]
                ]
            ],
        ];
    }

    /**
     * @param array $expectedArray
     * @param array $actualArray
     *
     * @dataProvider regexKeysArrayWithWrongKeysDataProvider
     */
    public function testFailRegexWithWrongKeys(array $expectedArray, array $actualArray): void
    {
        $client = new ExtendedMockHttpClient('http://test.test');
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->addBodyComparator(new JsonComparator(new ArrayComparator($expectedArray)))
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $this->expectException(NotFountSuitableFixtureException::class);

        $client->request('POST', 'http://test.test/foo/bar?qwe=rty', [
            'json' => $actualArray,
        ]);
    }

    public function regexKeysArrayWithWrongKeysDataProvider(): array
    {
        return [
            [
                [
                    '/foo/' => 'value1',
                ],
                [
                    321 => 'value1',
                ],
            ],
            [
                [
                    '/1\d+/' => 'value1',
                ],
                [
                    321 => 'value1',
                ],
            ],
            [
                [
                    '/test\w+/' => 'value2',
                    '/test\d+/' => 'value3',
                ],
                [
                    'Foo' => 'value2',
                    321 => 'value3',
                ],
            ],
            [
                [
                    [
                        '/root\w+/' => [
                            '/\d+/' => 'value1',
                            '/test\w+/' => 'value2',
                            '/test\d+/' => 'value3',
                        ]
                    ]
                ],
                [
                    [
                        'rootKey' => [
                            'test1' => 'value1',
                            'Foo' => 'value2',
                            321 => 'value3',
                        ]
                    ]
                ]
            ],
        ];
    }

    /**
     * @param array $expectedArray
     * @param array $actualArray
     *
     * @dataProvider regexKeysArrayWithWrongValuesDataProvider
     */
    public function testFailRegexWithWrongValues(array $expectedArray, array $actualArray): void
    {
        $client = new ExtendedMockHttpClient('http://test.test');
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->addBodyComparator(new JsonComparator(new ArrayComparator($expectedArray)))
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $this->expectException(NotFountSuitableFixtureException::class);

        $client->request('POST', 'http://test.test/foo/bar?qwe=rty', [
            'json' => $actualArray,
        ]);
    }

    public function regexKeysArrayWithWrongValuesDataProvider(): array
    {
        return [
            [
                [
                    '/\d+/' => 'value1',
                ],
                [
                    321 => 'value2',
                ],
            ],
            [
                [
                    '/1\d+/' => 'value1',
                ],
                [
                    1321 => 'value2',
                ],
            ],
            [
                [
                    '/test\w+/' => 'value2',
                    '/test\d+/' => 'value3',
                ],
                [
                    'testFoo' => 'valueFoo',
                    'test321' => 'valueBar',
                ],
            ],
            [
                [
                    [
                        '/root\w+/' => [
                            '/\d+/' => 'value1',
                            '/test\w+/' => 'value2',
                            '/test\d+/' => 'value3',
                        ]
                    ]
                ],
                [
                    [
                        'rootKey' => [
                            321 => 'valueFoo',
                            'testFoo' => 'valueBar',
                            'test321' => 'valueBaz',
                        ]
                    ]
                ]
            ],
        ];
    }

    /**
     * @param array $expectedArray
     * @param array $actualArray
     *
     * @dataProvider nestedComparatorsArrayDataProvider
     */
    public function testSuccessNestedComparators(array $expectedArray, array $actualArray): void
    {
        $client = new ExtendedMockHttpClient('http://test.test');
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->addBodyComparator(new JsonComparator(new ArrayComparator($expectedArray)))
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $response = $client->request('POST', 'http://test.test/foo/bar?qwe=rty', [
            'json' => $actualArray,
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('response body', $response->getContent(false));
    }

    public function nestedComparatorsArrayDataProvider(): array
    {
        return [
            [
                [
                    '/\d+/' => new StringEqualsComparator('value1'),
                ],
                [
                    321 => 'value1',
                ],
            ],
            [
                [
                    '/1\d+/' => new OrComparator([
                        new StringEqualsComparator('test1'),
                        new StringEqualsComparator('value1'),
                    ]),
                ],
                [
                    1321 => 'value1',
                ],
            ],
            [
                [
                    '/test\w+/' => new StringEqualsComparator('value2'),
                    '/test\d+/' => new RegexComparator('/value\d+/'),
                ],
                [
                    'testFoo' => 'value2',
                    'test321' => 'value3',
                ],
            ],
            [
                [
                    [
                        '/root\w+/' => new ArrayComparator([
                            '/\d+/' => new StringEqualsComparator('value1'),
                            '/test\w+/' => new RegexComparator('/value\d+/'),
                            '/test\d+/' => 'value3',
                        ])
                    ]
                ],
                [
                    [
                        'rootKey' => [
                            321 => 'value1',
                            'testFoo' => 'value2',
                            'test321' => 'value3',
                        ]
                    ]
                ]
            ],
        ];
    }

    /**
     * @param array $expectedArray
     * @param array $actualArray
     *
     * @dataProvider wrongNestedComparatorsArrayDataProvider
     */
    public function testFailWrongNestedComparators(array $expectedArray, array $actualArray): void
    {
        $client = new ExtendedMockHttpClient('http://test.test');
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->addBodyComparator(new JsonComparator(new ArrayComparator($expectedArray)))
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $this->expectException(NotFountSuitableFixtureException::class);

        $client->request('POST', 'http://test.test/foo/bar?qwe=rty', [
            'json' => $actualArray,
        ]);
    }

    public function wrongNestedComparatorsArrayDataProvider(): array
    {
        return [
            [
                [
                    '/\d+/' => new StringEqualsComparator('wrong'),
                ],
                [
                    321 => 'value1',
                ],
            ],
            [
                [
                    '/1\d+/' => new AndComparator([
                        new StringEqualsComparator('test1'),
                        new StringEqualsComparator('value1'),
                    ]),
                ],
                [
                    1321 => 'value1',
                ],
            ],
            [
                [
                    '/test\w+/' => new StringEqualsComparator('value2'),
                    '/test\d+/' => new RegexComparator('/^\d+/'),
                ],
                [
                    'testFoo' => 'value2',
                    'test321' => 'value3',
                ],
            ],
            [
                [
                    [
                        '/root\w+/' => new StringEqualsComparator('array')
                    ]
                ],
                [
                    [
                        'rootKey' => [
                            321 => 'value1',
                            'testFoo' => 'value2',
                            'test321' => 'value3',
                        ]
                    ]
                ]
            ],
        ];
    }
}
