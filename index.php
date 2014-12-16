<?php
define('DIRECTORY_SEPARATOR', '/');
define('CONFIG_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR);
define('LIB_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR);
define('CONTROLLERS_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR);
define('VIEWS_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR);

// Подключаем парсер логов
require_once(LIB_PATH . 'Config.php');
require_once(LIB_PATH . 'Parser.php');
// Подключаем класс, отвечающий за работу с бд
require_once(LIB_PATH . 'Database.php');
require_once(LIB_PATH . 'View.php');

require_once(CONTROLLERS_PATH . 'MainController.php');

// get type of action that should be performed
$q = $_GET['request_action'];

$main = new MainController();
$methodName = "{$q}Action";
if (method_exists($main, $methodName)) {
    $main->$methodName();
}

if (!(bool)$q) {
    $template = new View();
    $template->render('main');
}