<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Comparators;

use ExtendedMockHttpClient\Excpetion\Comparator\CallbackFunctionInvalidResultException;
use ExtendedMockHttpClient\Excpetion\Comparator\ErrorCallCallbackFunctionException;
use Throwable;

class CallbackComparator implements ComparatorInterface
{
    /**
     * @var callable
     */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function isMatch($value): bool
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
