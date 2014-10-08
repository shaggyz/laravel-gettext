<?php

App::before(function($request)
{
    LaravelGettext::getLocale();
});