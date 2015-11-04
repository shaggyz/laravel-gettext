<?php

namespace Xinax\LaravelGettext\Session;

use Illuminate\Support\Facades\Session;

class SessionHandler
{
    /** @var string */
    private $sessionIdentifier;

    /**
     * Creates a new Session handler.
     *
     * @param string $sessionIdentifier The identifier
     */
    public function __construct($sessionIdentifier)
    {
        $this->sessionIdentifier = $sessionIdentifier;
    }

    /**
     * Return the local identifier from the main session adapter
     *
     * @param string $default
     * @return string
     */
    public function get($default)
    {
        $locale = $default;

        if (Session::has($this->sessionIdentifier)) {
            $locale = Session::get($this->sessionIdentifier);
        }

        return $locale;

    }

    /**
     * Set the given locale in the session
     *
     * @param string $locale
     * @return $this
     */
    public function set($locale)
    {
        Session::set($this->sessionIdentifier, $locale);
        return $this;
    }
}
