<?php

class LogRepository extends Repository
{
    private $tableName = 'log';

    public function saveLogs(array $data)
    {
        $sql = "INSERT INTO $this->tableName (username, user_id, action_type, date_time)
                VALUES";
        foreach($data as $record){
            $sql .= "('"
                .$record['username']
                ."', '"
                .$record['user_id']
                ."', '"
                .$record['action']
                ."', STR_TO_DATE('"
                .$record['datetime']
                ."', '%d/%b/%Y %T')), ";
        }
        $sql = substr($sql, 0, strrpos($sql, ','));
        $sql .= ";";

        return $this->save($sql);
    }

    public function getUsers($startDate, $endDate)
    {
        $sql = "SELECT * FROM $this->tableName WHERE date_time BETWEEN '$startDate' AND '$endDate' ORDER BY username, date_time ASC";

        return $this->getAsArray($sql);
    }
} 