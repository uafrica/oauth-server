<?php

namespace OAuthServer\Bridge;

class GrantTypes
{
    const CLIENT_CREDENTIALS = 'client_credentials';

    const AUTHORIZATION_CODE = 'authorization_code';

    const REFRESH_TOKEN = 'refresh_token';

    const PASSWORD = 'password';

    protected static $classNameMap = [
        'ClientCredentialsGrant' => self::CLIENT_CREDENTIALS,
        'AuthCodeGrant' => self::AUTHORIZATION_CODE,
        'RefreshTokenGrant' => self::REFRESH_TOKEN,
        'PasswordGrant' => self::PASSWORD,
    ];

    /**
     * Get implemented and allowed grant types
     *
     * @return array
     */
    public static function getAllowedGrantTypes(): array
    {
        return [
            self::CLIENT_CREDENTIALS,
            self::AUTHORIZATION_CODE,
            self::REFRESH_TOKEN,
            self::PASSWORD,
        ];
    }

    /**
     * get grant type from grant class name
     *
     * @param string $grantClassName eg: AuthCodeGrantGrant
     * @return string|null
     */
    public static function convertFromGrantClassName(string $grantClassName): ?string
    {
        return isset(static::$classNameMap[$grantClassName]) ? static::$classNameMap[$grantClassName] : null;
    }
}
