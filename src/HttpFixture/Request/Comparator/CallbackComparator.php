<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\HttpFixture\Request\Comparator;

use ExtendedMockHttpClient\Excpetion\HttpFixture\Request\Comparator\CallbackFunctionInvalidResultException;
use ExtendedMockHttpClient\Excpetion\HttpFixture\Request\Comparator\ErrorCallCallbackFunctionException;
use Throwable;

class CallbackComparator implements ComparatorInterface
{
    /**
     * @var callable
     */
    private $callback;

    public static function getName(): string
    {
        return 'callback';
    }

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function __invoke($value): bool
    {
        try {
            $result = call_user_func($this->callback, $value);
        } catch (Throwable $exception) {
            throw new ErrorCallCallbackFunctionException($exception, $value);
        }

        if (!is_bool($result)) {
            throw new CallbackFunctionInvalidResultException($result);
        }

        return $result;
    }
}
