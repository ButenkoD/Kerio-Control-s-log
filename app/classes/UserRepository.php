<?php

class UserRepository extends Repository
{
    private $tableName = 'users';

    public function getNextUserId()
    {
        $sql = "SHOW TABLE STATUS FROM `kerio_control` LIKE '$this->tableName';";

        return $this->getItemByName($sql, 'Auto_increment');
    }

    public function saveUsers($data)
    {
        if (empty($data)){
            return;
        };
        $sql = "INSERT INTO $this->tableName (username)
                VALUES";
        foreach($data as $record){
            $sql .= "('"
                .$record['username']
                ."'), ";
        }
        $sql = substr($sql, 0, strrpos($sql, ','));
        $sql .= ";";

        return $this->save($sql);
    }

    public function getUsers()
    {
        $sql = "SELECT * FROM $this->tableName;";

        return $this->getAsArray($sql);
    }
} 