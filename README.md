# ExtendedMockHttpClient

| Version | Build Status | Code Coverage |
|:---------:|:-------------:|:-----:|
| `master`| [![CI][master Build Status Image]][master Build Status] | [![Coverage Status][master Code Coverage Image]][master Code Coverage] |

### Install
```shell script
composer require macpaw/extended_mock_http_client
```

### How to use
In config file `config/services_test.yaml` replace current HTTP client service
```yaml
imports:
    - { resource: services.yaml }

services:
    _defaults:
        autowire: true
        autoconfigure: true
        public: true
    http_client_service_name:
        class: ExtendedMockHttpClient\ExtendedMockHttpClient
        arguments:
            - 'https://test.host'
```

And then in PHPUnit test do something like this
```php
class MyTest extends KernelTestCase
{
    protected function setUp(): void
    {
        /** @var ExtendedMockHttpClient $mockHttpClient */
        $mockHttpClient = $this->getContainer()->get('http-client-service-name');
        $mockHttpClient->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->methodEquals('GET')
                ->urlEquals('http://test.host/api/v1/list')
                ->queryShouldContain('page', '1')
                ->headersShouldContain('X-header-name', 'Qwerty')
                ->bodyRegex('/foobar/')
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));
        
        $mockHttpClient->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->addMethodComparator(new OrComparator([
                    new StringEqualsComparator('GET'),
                    new StringEqualsComparator('POST'),
                ]))
                ->addQueryComparator(new QueryComparator([
                    new ArrayComparator(['qwe' => 'rty'])
                ]))
                ->addUrlComparator(new UrlComparator([
                    new RegexComparator('/test.host\/foo\/bar/')
                ]))
                ->addBodyComparator(new JsonComparator([
                    new ArrayComparator([
                        'rootNode' => [
                            123 => new StringEqualsComparator('value1'),
                            'testKey' => new RegexComparator('/value\d+/'),
                            '/regex\d+/' => 'value3',
                        ]
                    ]),
                    new CallbackComparator(function (array $data): bool {
                        return isset($data['foo']) && $data['foo'] === 'bar';
                    })
                ]))
                ->addHeadersComparator(new AndComparator([
                    new ArrayCountComparator(3),
                    new ArrayComparator([
                        'x-header-name' => 'Qwerty',
                        'content-type' => 'application/json',
                    ]),
                ]))
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));
    }
}
```

### Todo list
* Add checkers, write tests, etc
* Add Fixture expect count
* Add history function
  * Get last request/response (or by index)
  * Some kind of assert, it should check that history contain some request
* Add Comparators and function to it
  * Http method comparators
  * Query comparators
  * Body comparators
    * for different formats
* More docs and examples
* Write Symfony bundle possibility to add custom comparators through DI
* Add possibility create response based on request data


[master Build Status]: https://github.com/macpaw/ExtendedMockHttpClient/actions?query=workflow%3ACI+branch%3Amaster
[master Build Status Image]: https://github.com/macpaw/ExtendedMockHttpClient/workflows/CI/badge.svg?branch=master
[master Code Coverage]: https://codecov.io/gh/macpaw/ExtendedMockHttpClient/branch/master
[master Code Coverage Image]: https://img.shields.io/codecov/c/github/macpaw/ExtendedMockHttpClient/master?logo=codecov
