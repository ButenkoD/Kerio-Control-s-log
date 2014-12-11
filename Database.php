<?php
/**
 * Created by PhpStorm.
 * User: apedan
 * Date: 10.12.14
 * Time: 13:23
 */

class Database
{
    const DB_SERVER_NAME = 'localhost';
    const DB_USERNAME = 'root';
    const DB_PASSWORD = '';
    const DB_NAME = 'kerio-log';

    private $tableName = 'log';

    private function setConnection()
    {
        // Create connection
        $conn = new mysqli(self::DB_SERVER_NAME, self::DB_USERNAME, self::DB_PASSWORD, self::DB_NAME);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        return $conn;
    }

    public function saveParsedData(array $data)
    {
        $conn = $this->setConnection();
        $sql = "INSERT INTO $this->tableName (username, action_type, date_time)
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

    public function getData(array $parameters = array())
    {
        $conn = $this->setConnection();
        if (empty($parameters)){
            $sql = "SELECT * FROM $this->tableName;";
            if ($result = mysqli_query($conn, $sql)) {
                $data = array();
                while ($row = mysqli_fetch_array($result)){
                    $data[] = $row;
                }
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
        $conn->close();

        return isset($data)? $data : 'Error';
    }

    public function clearLogData(){
        $conn = $this->setConnection();
        $sql = "TRUNCATE TABLE $this->tableName;";
        if (mysqli_query($conn, $sql)){
            return true;
        } else {
            return "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}