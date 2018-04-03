<?php

use Xinax\LaravelGettext\LaravelGettext;

if (!function_exists('_i')) {
    /**
     * Translate a formatted string based on printf formats
     * Can be use an array on args or use the number of the arguments
     *
     * @param  string      $message the message to translate
     * @param  array|mixed $args    the tokens values used inside the $message
     *
     * @return string the message translated and formatted
     */
    function _i($message, $args = null)
    {

        $translator  = app(LaravelGettext::class);
        $translation = $translator->translate($message);

        if (strlen($translation)) {
            if (!empty($args)) {
                if (!is_array($args)) {
                    $args = array_slice(func_get_args(), 1);
                }
                $translation = vsprintf($translation, $args);
            }

            return $translation;
        }

        /**
         * If translations are missing returns
         * the original message.
         *
         * @see https://github.com/symfony/symfony/issues/13483
         */
        return $message;
    }
}

if (!function_exists('__')) {
    /**
     * Translate a formatted string based on printf formats
     * Can be use an array on args or use the number of the arguments
     *
     * @param  string      $message the message to translate
     * @param  array|mixed $args    the tokens values used inside the $message
     *
     * @return string the message translated and formatted
     */
    function __($message, $args = null)
    {
        return _i($message, $args);
    }
}

if (!function_exists('_')) {
    /**
     * Generic translation function
     *
     * @param $message
     *
     * @return mixed
     */
    function _($message, $args = null)
    {
        return _i($message, $args);
    }
}

if (!function_exists('_n')) {
    /**
     * Translate a formatted pluralized string based on printf formats
     * Can be use an array on args or use the number of the arguments
     *
     * @param  string      $singular the singular message to be translated
     * @param  string      $plural   the plural message to be translated if the $count > 1
     * @param  int         $count    the number of occurrence to be used to pluralize the $singular
     * @param  array|mixed $args     the tokens values used inside $singular or $plural
     *
     * @return string the message translated, pluralized and formatted
     */
    function _n($singular, $plural, $count, $args = null)
    {

        $translator = app(LaravelGettext::class);
        $message    = $translator->translatePlural($singular, $plural, $count);

        if (!empty($args) && !is_array($args)) {
            $args = array_slice(func_get_args(), 3);
        }
        $message = vsprintf($message, $args);

        return $message;
    }
}

if (!function_exists('_s')) {
    /**
     * Translate a formatted pluralized string based on printf formats mixed with the Symfony format
     * Can be use an array on args or use the number of the arguments
     *
     * <b>Only works if Symfony is the used backend</b>
     *
     * @param  string      $message  The one line message containing the different pluralization separated by pipes
     *                               See Symfony translation documentation
     * @param  int         $count    the number of occurrence to be used to pluralize the $singular
     * @param  array|mixed $args     the tokens values used inside $singular or $plural
     *
     * @return string the message translated, pluralized and formatted
     */
    function _s($message, $count, $args = null)
    {
        $translator = app(LaravelGettext::class);
        $message    = $translator->getTranslator()->translatePluralInline($message, $count);

        if (!empty($args) && !is_array($args)) {
            $args = array_slice(func_get_args(), 3);
        }
        $message = vsprintf($message, $args);

        return $message;
    }
}
