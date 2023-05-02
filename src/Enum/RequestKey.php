<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Enum;

enum RequestKey: string
{
    case METHOD = 'method';
    case URL = 'url';
    case QUERY = 'query';
    case BODY = 'body';
    case HEADERS = 'headers';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function isMethod(): bool
    {
        return $this === self::METHOD;
    }

    public function isUrl(): bool
    {
        return $this === self::URL;
    }

    public function isQuery(): bool
    {
        return $this === self::QUERY;
    }

    public function isBody(): bool
    {
        return $this === self::BODY;
    }

    public function isHeaders(): bool
    {
        return $this === self::HEADERS;
    }
}
