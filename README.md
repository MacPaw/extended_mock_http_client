# ExtendedMockHttpClient

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
            - 'https://foo.bar'
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
                ->urlEquals('http://test.test/api/v1/list')
                ->queryShouldContain('page', '1')
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
                    new ArrayHasValueByKeyComparator('qwe', 'rty')
                ]))
                ->addUrlComparator(new UrlComparator([
                    new StringEqualsComparator('http://test.test/foo/bar')
                ]))
                ->addBodyComparator(new JsonComparator([
                    new ArrayHasValueByKeyComparator('int', 1),
                    new CallbackComparator(function (array $data): bool {
                        return isset($data['foo']) && $data['foo'] === 'bar';
                    })
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
    * `InArray` comparator 
  * Url comparators
    * `regex`
  * Query comparators
    * `regex`
  * Header comparators
  * Body comparators
    * for different formats
  * Array comparators
* More docs and examples
* Write Symfony bundle possibility to add custom comparators through DI
* Add possibility create response based on request data
