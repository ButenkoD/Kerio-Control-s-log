<?php
define('CONFIG_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR);
define('LIB_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR);
define('CLASSES_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR);
define('CONTROLLERS_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR);
define('SERVICES_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'service' . DIRECTORY_SEPARATOR);
define('MODELS_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR);
define('VIEWS_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR);


// Подключаем парсер логов
require_once(LIB_PATH . 'Config.php');
require_once(LIB_PATH . 'Parser.php');
// Подключаем класс, отвечающий за работу с бд
require_once(LIB_PATH . 'Database.php');
require_once(LIB_PATH . 'Repository.php');
require_once(LIB_PATH . 'View.php');

require_once(CLASSES_PATH . 'UserRepository.php');
require_once(CLASSES_PATH . 'LogRepository.php');

require_once(CONTROLLERS_PATH . 'MainController.php');
// Подключаем сервисы
require_once(SERVICES_PATH . 'UserActionService.php');
require_once(SERVICES_PATH . 'KDateUtil.php');

require_once(MODELS_PATH . 'TimePeriod.php');
require_once(MODELS_PATH . 'UserActionModel.php');
require_once(MODELS_PATH . 'CellReportModel.php');


// get type of action that should be performed
$q = isset($_GET['request_action']) ? $_GET['request_action'] : null;

date_default_timezone_set(date_default_timezone_get());

$main = new MainController();
$methodName = "{$q}Action";
if (method_exists($main, $methodName)) {
    $main->$methodName();
}

if (!(bool)$q) {
    $template = new View();
    $template->render('main');
}
