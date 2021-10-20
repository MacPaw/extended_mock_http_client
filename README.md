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
                    new ArrayHasValueByKeyComparator('qwe', 'rty')
                ]))
                ->addUrlComparator(new UrlComparator([
                    new RegexComparator('/test.host\/foo\/bar/')
                ]))
                ->addBodyComparator(new JsonComparator([
                    new ArrayHasValueByKeyComparator('int', 1),
                    new CallbackComparator(function (array $data): bool {
                        return isset($data['foo']) && $data['foo'] === 'bar';
                    })
                ]))
                ->addHeadersComparator(new AndComparator([
                    new ArrayHasValueByKeyComparator('x-header-name', 'Qwerty'),
                    new ArrayHasValueByKeyComparator('content-type', 'application/json'),
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
  * Query comparators
  * Body comparators
    * for different formats
  * Array comparators
* More docs and examples
* Write Symfony bundle possibility to add custom comparators through DI
* Add possibility create response based on request data
