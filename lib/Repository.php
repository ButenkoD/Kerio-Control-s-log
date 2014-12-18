<?php

class Repository
{
    private $dbName;
    private $serverName;
    private $username;
    private $password;

    /**
     * @var mysqli
     */
    protected static $_connection;

    public function __construct()
    {
        $config = Config::getInstance();
        foreach($config->get('db') as $key => $param){
            if (property_exists($this, $key)){
                $this->$key = $param;
            }
        }
        self::$_connection = new mysqli($this->serverName, $this->username, $this->password, $this->dbName);

        return $this;
    }

    /**
     * Получить состояние
     * @return mysqli
     */
    public function getConnection()
    {
        // проверяем актуальность экземпляра
        if (null === self::$_connection) {
            // создаем новый экземпляр
            self::$_connection = new mysqli($this->serverName, $this->username, $this->password, $this->dbName);
        }
        // возвращаем созданный или существующий экземпляр
        return self::$_connection;
    }

//    private function setConnection()
//    {
//        // Create connection
//        $conn = new mysqli($this->serverName, $this->username, $this->password, $this->dbName);
//        // Check connection
//        if ($conn->connect_error) {
//            die("Connection failed: " . $conn->connect_error);
//        }
//
//        return $conn;
//    }

    /**
     * @param string $sql
     */
    public function save($sql)
    {
        $conn = $this->getConnection();

        if ($conn->query($sql) === TRUE) {
            return true;
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

//        $conn->close();
    }

    public function getAsArray($sql)
    {
        $conn = $this->getConnection();
        if ($result = mysqli_query($conn, $sql)) {
            $data = array();
            while ($row = mysqli_fetch_array($result)){
                $data[] = $row;
            }
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
//        $conn->close();

        return isset($data)? $data : 'Error';
    }

    public function getItemByName($sql, $name)
    {
        $conn = $this->getConnection();
        if ($result = mysqli_query($conn, $sql)){
            $return = (int)mysqli_fetch_assoc($result)[$name];
//            $conn->close();

            return $return;
        }
    }

    public function closeConnection(){
        // проверяем актуальность экземпляра
        if (null !== self::$_connection) {
            self::$_connection->close();
        }
    }
} 