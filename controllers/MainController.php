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
        $log = file_get_contents($config->get('log_file_path'));
        $databaseHandler = new Database();
        $parser = new Parser($databaseHandler);
//       $log = $parser->getLog($config->get('log_file_path'));
        $data = $parser->parseString($log);
        if (!empty($data)) {
            $databaseHandler->saveParsedData($data);
        }

    }
}
