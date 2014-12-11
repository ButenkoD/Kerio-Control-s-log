<?php
/**
 * Created by PhpStorm.
 * User: apedan
 * Date: 10.12.14
 * Time: 13:23
 */

class Parser
{
    const PRE_USERNAME_STRING_WHILE_LOG_IN  = "[User]";
    const PRE_USERNAME_STRING_WHILE_LOG_OUT = 'User';
    const PRE_LOG_IN_STRING                 = 'logged in';
    const PRE_LOG_OUT_STRING                = 'logged out';
    const TIME_START_STRING                 = '[';
    const TIME_END_STRING                   = ']';

    public function parseString($log)
    {
        $records = explode("\n", $log);
        $result = array();
        foreach($records as $record){
            if (!(strpos($record, self::PRE_LOG_IN_STRING))
                && !(strpos($record, self::PRE_LOG_OUT_STRING))){
                continue;
            }
            if ($begining = strpos($record, self::PRE_USERNAME_STRING_WHILE_LOG_IN)){
                $actionType = self::PRE_LOG_IN_STRING;
                $tail = substr($record, $begining + strlen(self::PRE_USERNAME_STRING_WHILE_LOG_IN) + 1);
                $username = substr($tail, 0, strpos($tail, ' '));
            } elseif ($begining = strpos($record, self::PRE_USERNAME_STRING_WHILE_LOG_OUT)){
                $actionType = self::PRE_LOG_OUT_STRING;
                $tail = substr($record, $begining + strlen(self::PRE_USERNAME_STRING_WHILE_LOG_OUT) + 1);
                $username = substr($tail, 0, strpos($tail, ' '));
            }

            $datetime = substr(
                $record,
                strpos($record, self::TIME_START_STRING) + strlen(self::TIME_START_STRING),
                strpos($record, self::TIME_END_STRING) - strlen(self::TIME_END_STRING)
            );

            $result[] = array(
                'username' => $username,
                'action'   => $actionType,
                'datetime' => $datetime
            );
        }
        return $result;
    }
}