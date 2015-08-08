<?php

namespace Xinax\LaravelGettext\Session;

use Illuminate\Support\Facades\Session;

class SessionHandler{

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
     * Returns the locale identifier from
     * the main session adapter
     */
    public function get($default)
    {
        $locale = $default;

        if(Session::has($this->sessionIdentifier)){
            $locale = Session::get($this->sessionIdentifier);
        }

        return $locale;

    }

    /**
     * Sets the given locale on session
     */
    public function set($locale)
    {
        Session::set($this->sessionIdentifier, $locale);
    }
}
