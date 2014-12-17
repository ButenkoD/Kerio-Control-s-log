<?php

class Database
{
    private $dbName;
    private $serverName;
    private $username;
    private $password;
    private $tableName = 'log';
    private $userTableName = 'users';

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

    public function getNextUserId()
    {
        $conn = $this->setConnection();
        $sql = "SHOW TABLE STATUS FROM `kerio_control` LIKE 'users';";
        if ($result = mysqli_query($conn, $sql)){
            $return = (int)mysqli_fetch_assoc($result)['Auto_increment'];
            return $return;
        }
    }

    public function saveUser($data)
    {
        $conn = $this->setConnection();
        $sql = "INSERT INTO $this->userTableName (username) VALUE ('".$data['username']."');";
        if ($conn->query($sql) !== TRUE){
        }
        $conn->close();
    }

    public function getUsers()
    {
        $conn = $this->setConnection();
        $sql = "SELECT * FROM $this->userTableName";
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

    public function getLatestDate()
    {
        $conn = $this->setConnection();
        $sql = "SELECT MAX(date_time) FROM $this->tableName;";
        if ($result = mysqli_query($conn, $sql)){
            $conn->close();
            return mysqli_fetch_array($result)[0];
        } else {
            $conn->close();
            return "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
