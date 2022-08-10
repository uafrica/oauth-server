<?php

namespace OAuthServer\Lib\Enum;

use MyCLabs\Enum\Enum;

/**
 * OAuth 2.0 token type enumeration
 *
 * @method static Token AUTHENTICATION_TOKEN()
 * @method static Token ACCESS_TOKEN()
 * @method static Token REFRESH_TOKEN()
 */
class Token extends Enum
{
    const AUTHENTICATION_TOKEN = 'authentication_token';
    const ACCESS_TOKEN         = 'access_token';
    const REFRESH_TOKEN        = 'refresh_token';

    /**
     * Maps token types to readable names
     *
     * @param string|null $value
     * @return string|array
     */
    public static function labels(?string $value = null)
    {
        $labels = [
            static::AUTHENTICATION_TOKEN => 'Authentication token',
            static::ACCESS_TOKEN         => 'Access token',
            static::REFRESH_TOKEN        => 'Refresh token',
        ];
        return static::enum($value, $labels);
    }

    /**
     * Get all constant raw values
     *
     * @return string[]
     */
    public static function rawValues(): array
    {
        return array_map('strval', static::values());
    }
}