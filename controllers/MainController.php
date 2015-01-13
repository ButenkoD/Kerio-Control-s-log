<?php

use \service\UserActionService;

/**
 * Class MainController
 */
class MainController
{
    /**
     * Действие "Показать все логи"
     */
    public function showAllAction()
    {
        $config = Config::getInstance();
        $showDayNumber = $config->get('showDayNumber');

        // Извлекаем из запроса параметр: дату, начиная с которой следует выводить логи
        $startDate = $_GET['start_date'];
        $endDate = date('Y-m-d', strtotime($startDate . '+ ' . $showDayNumber . ' day'));
        if (strtotime($endDate) > time()) {
            $endDate = date('Y-m-d');
        }

        $databaseHandler = new Database();
        $rows = $databaseHandler->getData($startDate, $endDate);

        // Формируем таблицу результатов запроса
        $dates = array();
        $interval = date_diff(date_create($startDate), date_create($endDate));
        for ($i = 0; $i <= $interval->days; $i++) {
            $dates[$i] = date('Y-m-d', strtotime($startDate . '+ ' . $i . ' day'));
        }

        $table = array();

        $userService = new UserActionService();
        foreach ($rows as $row) {
            $userService->addUserDailyAction($row);
        }

        //TODO: избавиться от массива - передать объект на вьюшку или JSON
        foreach ($userService->getUserDailyActions() as $act) {
            $report = $act->calculateDailyReport();
            $table[$act->getUsername()][$act->getDate()]['note'] = $report->getNotification();
            $table[$act->getUsername()][$act->getDate()]['messages'] = $report->getTimePair();
            $table[$act->getUsername()][$act->getDate()]['isWholeDay'] = $report->getIsWholeDay();
        };
        // Выводим таблицу
        $template = new View();
        $template->render('datatable', ['dates' => $dates, 'startDate' => $startDate, 'table' => $table]);
    }

    /**
     * Действие "Очистить таблицу в БД"
     */
    public function clearDBAction()
    {
        $databaseHandler = new Database();
        if ($result = $databaseHandler->clearLogData() === true) {
            echo "DB's table was truncated";
        } else {
            echo $result;
        }
    }

    /**
     * Действие "Распарсить логи Керио и записать данные в базу"
     */
    public function parseLogAction()
    {
        $config = Config::getInstance();
        $databaseHandler = new Database();
        $parser = new Parser($databaseHandler);
        $logFiles = array();
        if ($_GET['parse-tree'] == 'true') {
            $logFiles = $this->getLogFilesList($config);
        } else {
            $logFiles[] = $config->get('log_file_path');
        }
        if (empty($logFiles)) {
            echo 'No files found';
        }
        foreach ($logFiles as $name) {
            $log = file_get_contents($name);
            echo('<i>File: ' . $name . '<br></i>');
            $data = $parser->parseString($log);
            if (!empty($data)) {
                $databaseHandler->saveParsedData($data);
            }
        }
    }


    /**
     * @param $config
     * @return array
     */
    private function getLogFilesList($config)
    {
        $array = array();
        $path = $config->get('log_file_tree_dir');
        $dir = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::SELF_FIRST);
        foreach ($files as $file) {
            if (substr($file->getFileName(), -3) == 'log') {
                $array[] = $file->getPath() . DIRECTORY_SEPARATOR . $file->getFileName();
            }
        }
        return $array;
    }
}
