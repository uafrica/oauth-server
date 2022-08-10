<?php

namespace OAuthServer\Lib\Enum\Traits;

/**
 * Enumeration helper trait
 */
trait EnumTrait
{
    /**
     * The main method for any enumeration, should be called statically
     * Now also supports reordering/filtering
     *
     * e.g.
     * ```
     * public static function statuses($value = null) {
     *     $array = [
     *         self::STATUS_INACTIVE => __('Inactive', true),
     *         self::STATUS_ACTIVE   => __('Active', true),
     *     ];
     *     return parent::enum($value, $array);
     * }
     * ```
     *
     * @param int|string|array|null $value   Integer or array of keys or NULL for complete array result
     * @param array                 $options Options
     * @param string|null           $default Default value
     * @return string|array
     */
    public static function enum($value, array $options, ?string $default = null)
    {
        if ($value !== null && !is_array($value)) {
            if (array_key_exists($value, $options)) {
                return $options[$value];
            }
            return $default;
        }
        if ($value !== null) {
            $newOptions = [];
            foreach ($value as $v) {
                $newOptions[$v] = $options[$v];
            }
            return $newOptions;
        }
        return $options;
    }
}