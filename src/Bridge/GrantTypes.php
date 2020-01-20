<?php

namespace OAuthServer\Bridge;

class GrantTypes
{
    const CLIENT_CREDENTIALS = 'client_credentials';

    const AUTHORIZATION_CODE = 'authorization_code';

    const REFRESH_TOKEN = 'refresh_token';

    const PASSWORD = 'password';

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
}
