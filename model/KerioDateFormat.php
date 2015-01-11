<?php

namespace model;


abstract class KerioDateFormat
{
    protected $pattern;

    function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    public function stringToTimestamp($string)
    {
        return date_create_from_format($this->pattern, $string)->getTimestamp();
    }

    public function timestampToString($string)
    {
        return date($this->pattern, $string);
    }
}