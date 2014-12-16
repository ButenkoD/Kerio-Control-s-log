<?php

class Database
{
    private $dbName;
    private $serverName;
    private $username;
    private $password;
    private $tableName = 'log';

    public function __construct()
    {
        $config = Config::getInstance();
        foreach($config->get('db') as $key => $param){
            if (property_exists($this, $key)){
                $this->$key = $param;
            }
        }

        return $this;
    }

    private function setConnection()
    {
        // Create connection
        $conn = new mysqli($this->serverName, $this->username, $this->password, $this->dbName);
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
            echo "New records were created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $conn->close();
    }

    public function getData($startDate, $endDate)
    {
        $conn = $this->setConnection();
        $sql = "SELECT * FROM $this->tableName WHERE date_time BETWEEN '$startDate' AND '$endDate' ORDER BY username, date_time ASC";
        if ($result = mysqli_query($conn, $sql)) {
            $data = array();
            while ($row = mysqli_fetch_array($result)){
                $data[] = $row;
            }
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        $conn->close();

        return isset($data)? $data : 'Error';
    }

    public function clearLogData(){
        $conn = $this->setConnection();
        $sql = "TRUNCATE TABLE $this->tableName;";
        if (mysqli_query($conn, $sql)){
            $conn->close();
            return true;
        } else {
            $conn->close();
            return "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}