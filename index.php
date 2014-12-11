<?php

// set where Kerio host.log file lies
define('LOG_FILE_PATH', dirname(__FILE__) . '/host.log');
// set db connection parameters
define('DB_SERVER_NAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'ftftr');
define('DB_NAME', 'kerio_control');

// Подключаем парсер логов
require_once(dirname(__FILE__) . '/Parser.php');
// Подключаем класс, отвечающий за работу с бд
require_once(dirname(__FILE__) . '/Database.php');
// Подключаем класс, отвечающий за рассылку данных через эл. почту
require_once(dirname(__FILE__) . '/Mailer.php');

$log = file_get_contents(LOG_FILE_PATH);

$parser = new Parser();
$data = $parser->parseString($log);

// Create connection
$conn = new mysqli(DB_SERVER_NAME, DB_USERNAME, DB_PASSWORD, DB_NAME);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO log (username, action_type, date_time)
VALUES ('John', 'logged in', STR_TO_DATE('13/Dec/2012 08:06:26', '%d/%b/%Y %T'))";

if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

var_dump($data);