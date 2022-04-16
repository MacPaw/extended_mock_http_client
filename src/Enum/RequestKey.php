<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static RequestKey METHOD()
 * @method static RequestKey URL()
 * @method static RequestKey QUERY()
 * @method static RequestKey BODY()
 * @method static RequestKey HEADERS()
 */
class RequestKey extends Enum
{
    public const METHOD = 'method';
    public const URL = 'url';
    public const QUERY = 'query';
    public const BODY = 'body';
    public const HEADERS = 'headers';

    /**
     * @return string[]
     */
    public static function getValues(): array
    {
        return array_values(self::toArray());
    }

    public function isMethod(): bool
    {
        return $this->getValue() === self::METHOD;
    }

    public function isUrl(): bool
    {
        return $this->getValue() === self::URL;
    }

    public function isQuery(): bool
    {
        return $this->getValue() === self::QUERY;
    }

    public function isBody(): bool
    {
        return $this->getValue() === self::BODY;
    }

    public function isHeaders(): bool
    {
        return $this->getValue() === self::HEADERS;
    }
}
