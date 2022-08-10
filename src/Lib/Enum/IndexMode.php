<?php

namespace OAuthServer\Lib\Enum;

use MyCLabs\Enum\Enum;
use OAuthServer\Lib\Enum\Traits\EnumTrait;

/**
 * OAuth 2.0 URI index endpoint handling mode enumeration
 *
 * @method static IndexMode REDIRECT_TO_AUTHORIZE()
 * @method static IndexMode REDIRECT_TO_STATUS()
 * @method static IndexMode DISABLED()
 */
class IndexMode extends Enum
{
    use EnumTrait;

    const REDIRECT_TO_AUTHORIZE = 'redirect_to_authorize';
    const REDIRECT_TO_STATUS    = 'redirect_to_status';
    const DISABLED              = 'disabled';

    /**
     * Maps repositories to readable names
     *
     * @param string|null $value
     * @return string|array
     */
    public static function labels(?string $value = null)
    {
        $modes = [
            static::REDIRECT_TO_AUTHORIZE => 'Redirect to authorize',
            static::REDIRECT_TO_STATUS    => 'Redirect to status',
            static::DISABLED              => 'Disabled',
        ];
        return static::enu($value, $modes);
    }
}