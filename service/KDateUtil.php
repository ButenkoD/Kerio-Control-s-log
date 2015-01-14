<?php

namespace service;


class KDateUtil
{
    const MIN_LOG_IN_TIME = ' 07:30:00';
    const SQL_PATTERN = 'Y-m-d H:i:s';
    const LOG_PATTERN = 'd/M/Y H:i:s';
    const DATE_ONLY = 'Y-m-d';

    private static function __stringToTimestamp($pattern, $string)
    {
        return date_create_from_format($pattern, $string)->getTimestamp();
    }

    private static function __timestampToString($pattern, $timestamp)
    {
        return date($pattern, $timestamp);
    }

    public static function toTimestampSQL($string)
    {
        return self::__stringToTimestamp(self::SQL_PATTERN, $string);
    }

    public static function toTimestampLOG($string)
    {
        return self::__stringToTimestamp(self::LOG_PATTERN, $string);
    }


    public static function toStringSQL($timestamp)
    {
        return self::__timestampToString(self::SQL_PATTERN, $timestamp);
    }

    public static function toStringLOG($timestamp)
    {
        return self::__timestampToString(self::LOG_PATTERN, $timestamp);
    }

    public static function timestampToDateOnly($timestamp)
    {
        return self::__timestampToString(self::DATE_ONLY, $timestamp);
    }

    public static function stringToDateOnly($string)
    {
        return self::__stringToTimestamp(self::DATE_ONLY, $string);
    }

    public static function isTimestampAfterHours($timestamp, $string)
    {
        $string = self::timestampToDateOnly($timestamp) . ' ' . $string;
        return ($timestamp > self::toTimestampSQL($string));
    }


    public static function toMinLogTime($timestamp)
    {
        return self::toTimestampSQL(self::timestampToDateOnly($timestamp) . self::MIN_LOG_IN_TIME);
    }

    public static function toMinLogTimeStr($string)
    {
        return self::toTimestampSQL($string . self::MIN_LOG_IN_TIME);
    }


}