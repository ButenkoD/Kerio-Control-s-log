<?php

namespace model;


class LogDateFormat extends KerioDateFormat
{
    function __construct()
    {
        return parent::__construct('d/M/Y H:i:s');
    }
}