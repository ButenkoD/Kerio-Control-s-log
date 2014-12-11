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

$databaseHandler = new Database();
$databaseHandler->saveParsedData($data);



var_dump($data);