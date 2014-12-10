<?php
/**
 * Created by PhpStorm.
 * User: apedan
 * Date: 10.12.14
 * Time: 13:23
 */

class Parser
{
    const PRE_USERNAME_STRING = '[User]';
    const PRE_LOG_IN_STRING   = 'logged in';
    const PRE_LOG_OUT_STRING  = 'logged out';

    public function parseString($log)
    {
        $records = explode("\n", $log);
        $result = array();
        foreach($records as $record){
            if (!(strpos($record, self::PRE_LOG_IN_STRING))
                && !(strpos($record, self::PRE_LOG_OUT_STRING))){
                continue;
            }
            $recordElements = explode(' ', $record);
            $result[''] = $recordElements[array_keys($recordElements, 'logged')[0]+2]
                . ' '
                . $recordElements[array_keys($recordElements, 'logged')[0]+3];

            $result[] = $record;
        }
        return $result;
    }
}