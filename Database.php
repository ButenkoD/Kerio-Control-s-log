<?php
/**
 * Created by PhpStorm.
 * User: apedan
 * Date: 10.12.14
 * Time: 13:23
 */

class Database
{
    private function setConnection()
    {
        // Create connection
        $conn = new mysqli(DB_SERVER_NAME, DB_USERNAME, DB_PASSWORD, DB_NAME);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        return $conn;
    }

    public function saveParsedData($data)
    {
        $conn = $this->setConnection();
        $sql = "INSERT INTO log (username, action_type, date_time)
                VALUES";
        foreach($data as $record){
            $sql .= "('"
                .$record['username']
                ."', '"
                .$record['action']
                ."', STR_TO_DATE('"
                .$record['datetime']
                ."', '%d/%b/%Y %T')), ";
        }

        $sql = substr($sql, 0, strrpos($sql, ','));
        $sql .= ";";

        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $conn->close();
    }
}