# ExtendedMockHttpClient

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
    test.http-client-service-name:
        class: App\Tests\ExtendedMockHttpClient\ExtendedMockHttpClient
        arguments:
            - 'http://foo.bar/api'
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
