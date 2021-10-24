<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Comparators;

class ArrayComparator implements ComparatorInterface
{
    private $array;

    public function __construct(array $array)
    {
        $this->array = $array;
    }

    public function isMatch($value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        return $this->isArrayEqual($this->array, $value);
    }

    private function isArrayEqual(array $expectedArray, array $actualArray): bool
    {
        if ($this->isArrayAssociative($expectedArray)) {
            foreach ($expectedArray as $expectedKey => $expectedValue) {
                if (is_string($expectedKey) && $this->isRegexValid($expectedKey)) {
                    $expectedKey = $this->findKeyByRegexKeyAndValue($expectedKey, $expectedValue, $actualArray);

                    if ($expectedKey === false) {
                        return false;
                    }

                    unset($actualArray[$expectedKey]);

                    continue;
                }

                if (!isset($actualArray[$expectedKey])) {
                    return false;
                }

                $actualValue = $actualArray[$expectedKey];

                if ($this->isValueEqual($expectedValue, $actualValue)) {
                    unset($actualArray[$expectedKey]);

                    continue;
                }

                return false;
            }
        } else {
            foreach ($expectedArray as $expectedValue) {
                $expectedKey = $this->findKeyByRegexKeyAndValue('/\d+/', $expectedValue, $actualArray);

                if ($expectedKey === false) {
                    return false;
                }

                unset($actualArray[$expectedKey]);
            }
        }

        return true;
    }

    /**
     * @return string|int|false
     */
    private function findKeyByRegexKeyAndValue(string $regex, $expectedValue, array $actualArray)
    {
        foreach ($actualArray as $key => $actualValue) {
            if ((preg_match($regex, (string)$key) === 1) && $this->isValueEqual($expectedValue, $actualValue)) {
                return $key;
            }
        }

        return false;
    }

    private function isValueEqual($expectedValue, $actualValue): bool
    {
        if ($expectedValue instanceof ComparatorInterface) {
            if (!$expectedValue->isMatch($actualValue)) {
                return false;
            }
        } elseif (is_array($expectedValue)) {
            if (!is_array($actualValue)) {
                return false;
            }

            return $this->isArrayEqual($expectedValue, $actualValue);
        } elseif ($expectedValue !== $actualValue) {
            return false;
        }

        return true;
    }

    private function isArrayAssociative(array $value): bool
    {
        if ($value === []) {
            return false;
        }

        return array_keys($value) !== range(0, count($value) - 1);
    }

    private function isRegexValid(string $regex): bool
    {
        return @preg_match($regex, '') !== false;
    }
}
