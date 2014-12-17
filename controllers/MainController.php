<?php

/**
 * Class MainController
 */
class MainController {
    /**
     * Действие "Показать все логи"
     */
    public function showAllAction() {
        $config = Config::getInstance();
        $showDayNumber = $config->get('showDayNumber');

        // Извлекаем из запроса параметр: дату, начиная с которой следует выводить логи
        $startDate = $_GET['start_date'];
        $endDate = date('Y-m-d', strtotime($startDate . '+ ' . $showDayNumber .' day'));
        if (strtotime($endDate) > time()) {
            $endDate = date('Y-m-d');
        }

        $databaseHandler = new Database();
        $rows = $databaseHandler->getData($startDate, $endDate);

        // Формируем таблицу результатов запроса
        $dates = array();
        $interval = date_diff(date_create($startDate), date_create($endDate));
        for ($i = 0; $i <= $interval->days; $i++){
            $dates[$i] = date('Y-m-d', strtotime($startDate . '+ ' . $i .' day'));
        }

        $table = array();
        foreach($rows as $row) {
            foreach ($dates as $date){
                if (date('Y-m-d', strtotime($row['date_time'])) == $date){
                    $row['date_time'] = date('H:i', strtotime($row['date_time']));
                    if ($row['action_type'] == 'logged in'){
                        if (!isset($table[$row['username']][$date]['valueIn'])){
                            $table[$row['username']][$date]['valueIn'] = $row['date_time'];
                            $table[$row['username']][$date]['late'] = (bool)(date('H:i', strtotime($row['date_time'])) > date('H:i', strtotime('09:15')));
                        }
                    } elseif ($row['action_type'] == 'logged out'){
                        $table[$row['username']][$date]['valueOut'] = ' -- ' . $row['date_time'];
                    }
                }
            }
        }

        // Выводим таблицу
        $template = new View();
        $template->render('show', ['dates' => $dates, 'startDate' => $startDate, 'table' => $table]);
    }

    /**
     * Действие "Очистить таблицу в БД"
     */
    public function clearDBAction() {
        $databaseHandler = new Database();
        var_dump($databaseHandler->getNextUserId());die();
        if ($result = $databaseHandler->clearLogData() === true){
            echo "DB's table was truncated";
        } else {
            echo $result;
        }
    }

    /**
     * Действие "Распарсить логи Керио и записать данные в базу"
     */
    public function parseLogAction() {
        $config = Config::getInstance();
//        $log = file_get_contents($config->get('log_file_path'));

        $databaseHandler = new Database();
        $parser = new Parser($databaseHandler);
        $log = $parser->getLog($config->get('log_file_path'));
        $data = $parser->parseString($log);
//        $databaseHandler->saveParsedData($data);
    }
}
