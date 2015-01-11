<?php

namespace model;


class SqlDateFormat extends KerioDateFormat
{
    function __construct()
    {
        return parent::__construct('Y-m-d H:i:s');
    }
}