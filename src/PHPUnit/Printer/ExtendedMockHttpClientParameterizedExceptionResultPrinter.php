<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\PHPUnit\Printer;

use ExtendedMockHttpClient\Excpetion\AbstractExtendedMockHttpClientException;
use ExtendedMockHttpClient\Excpetion\ExtendedMockHttpClientParameterizedException;
use PHPUnit\Framework\ExceptionWrapper;
use PHPUnit\Framework\TestFailure;
use PHPUnit\TextUI\ResultPrinter;
use Throwable;

class ExtendedMockHttpClientParameterizedExceptionResultPrinter extends ResultPrinter
{
    protected function printDefectTrace(TestFailure $defect): void
    {
        $exception = $this->getExtendedMockHttpClientException($defect->thrownException());

        if ($exception instanceof ExtendedMockHttpClientParameterizedException) {
            $this->printParameterizedException($exception);
        } else {
            parent::printDefectTrace($defect);
        }
    }

    private function printParameterizedException(ExtendedMockHttpClientParameterizedException $exception): void
    {
        $this->write("\n" . $exception->getMessage());
        $this->printParameterizedExceptionParameters($exception->getParameters(), 1);
    }

    private function printParameterizedExceptionParameters(array $parameters, int $nesting): void
    {
        foreach ($parameters as $name => $value) {
            $this->write("\n" . str_repeat("  ", $nesting) . $name . ': ');

            if (is_array($value)) {
                $this->printParameterizedExceptionParameters($value, $nesting + 1);
            } else {
                $this->write($value);
            }
        }
    }

    private function getExtendedMockHttpClientException(Throwable $error): ?AbstractExtendedMockHttpClientException
    {
        if ($error instanceof AbstractExtendedMockHttpClientException) {
            return $error;
        }

        if ($error instanceof ExceptionWrapper) {
            return $this->getExtendedMockHttpClientException($error->getOriginalException());
        }

        if ($error->getPrevious() instanceof Throwable) {
            return $this->getExtendedMockHttpClientException($error->getPrevious());
        }

        return null;
    }
}
