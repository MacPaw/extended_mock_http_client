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
                ->urlEquals('')
                ->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));
    }
}
```

### Setup readable ExtendedMockHttpClient errors
Need to update your `phpunit.xml` file as follows
```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit
    ...
    printerClass="ExtendedMockHttpClient\PHPUnit\Printer\ExtendedMockHttpClientParameterizedExceptionResultPrinter"
>
    // ....
</phpunit>
```

### Todo list
* Add checkers, write tests, etc
* Add Fixture expect count
* Add history function
  * Get last request/response (or by index)
  * Some kind of assert, it should check that history contain some request
* Add Comparators and function to it
  * Add `OR` and `AND` comparators
  * Http method comparators
    * `InArray` comparator 
  * Url comparators
    * `callable`
    * `regex`
  * Query comparators
  * Header comparators
  * Body comparators
    * Json body comparator
* More docs and examples
* Write Symfony bundle possibility to add custom comparators through DI
* Add possibility create response based on request data
