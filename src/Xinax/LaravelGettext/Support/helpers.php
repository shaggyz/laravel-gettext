<?php
if (!function_exists('__')) {
    /**
     * Translate a formated string based on printf formats
     * Can be use an array on args or use the number of the arguments
     *
     * @param  string $message the message to translate
     * @param  array|mixed $args the tokens values used inside the $message
     * @return string the message translated and formated
     */
    function __($message, $args = null)
    {
        $message = gettext($message);
        if (!empty($args) && !is_array($args)) {
            $args = array_slice(func_get_args(), 1);
        }
        $message = vsprintf($message, $args);
        return $message;
    }
}

if (!function_exists('_n')) {
    /**
     * Translate a formated pluralized string based on printf formats
     * Can be use an array on args or use the number of the arguments
     *
     * @param  string $singular the singular message to be translated
     * @param  string $plural the plural message to be translated if the $count > 1
     * @param  int $count the number of occurence to be used to pluralize the $singular
     * @param  array|mixed $argsthe tokens values used inside $singular or $plural
     * @return string the message translated, pluralized and formated
     */
    function _n($singular, $plural, $count, $args = null)
    {
        $message = ngettext($singular, $plural, $count);
        if (!empty($args) && !is_array($args)) {
            $args = array_slice(func_get_args(), 3);
        }
        $message = vsprintf($message, $args);
        return $message;
    }
}
