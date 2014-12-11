<?php

// set where Kerio host.log file lies
define('LOG_FILE_PATH', dirname(__FILE__) . '/host.log');

// Подключаем парсер логов
require_once(dirname(__FILE__) . '/Parser.php');
// Подключаем класс, отвечающий за работу с бд
require_once(dirname(__FILE__) . '/Database.php');

$log = file_get_contents(LOG_FILE_PATH);

$parser = new Parser();
$data = $parser->parseString($log);

$databaseHandler = new Database();
$databaseHandler->saveParsedData($data);