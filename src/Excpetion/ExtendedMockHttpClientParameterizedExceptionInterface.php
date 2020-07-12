<?php

namespace ExtendedMockHttpClient\Excpetion;

use Exception;

interface ExtendedMockHttpClientParameterizedExceptionInterface
{
    public function getParameters(): array;
}
