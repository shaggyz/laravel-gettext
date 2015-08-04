<?php namespace Xinax\LaravelGettext\Composers;

use Xinax\LaravelGettext\LaravelGettext;
use Illuminate\Support\Facades\Config;

/**
 * Simple language selector generator.
 * @author Nicolás Daniel Palumbo
 */
class LanguageSelector
{

    /**
     * Language labels
     * @var Array
     */
    protected $labels = [];

    /**
     * Laravel gettext wrapper
     * @var LaravelGettext
     */
    protected $gettext;

    /**
     * Creates a new instance of language selector
     * @param Array $labels
     */
    public function __construct($labels = [], LaravelGettext $gettext)
    {
        $this->labels = $labels;
        $this->gettext = $gettext;
    }

    /**
     * Creates a new selector instance
     * @return void
     */
    public static function create($labels = [], LaravelGettext $gettext)
    {
        return new LanguageSelector($labels, $gettext);
    }

    /**
     * Renders the language selector
     * @return String
     */
    public function render()
    {
        $html = '<ul class="language-selector">';

        foreach (Config::get('laravel-gettext.supported-locales') as $locale) {

            if(count($this->labels) && array_key_exists($locale, $this->labels)){
                $localeLabel = $this->labels[$locale];
            } else {
                $localeLabel = $locale;
            }

            if($locale == $this->gettext->getLocale()){
                $html .= '<li><strong class="active ' . $locale . '">' . $localeLabel . '</strong></li>';
            } else {
                $html .= '<li><a href="/lang/' . $locale . '" class="' . $locale . '">' . $localeLabel . '</a></li>';
            }

        }

        $html .= '</ul>';

        return $html;
    }

    /**
     * String conversion
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

}