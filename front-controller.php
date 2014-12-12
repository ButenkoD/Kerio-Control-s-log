<?php
// get type of action that should be performed
$q = $_GET['request_action'];

// plug in configuration data
$configs = include('config.php');

// list of available actions
$routing = array(
    'showAll' => 'showAll',
    'clearDB' => 'clearDB',
    'parseLog'=> 'parseLog'
);

switch($q){
    // Действие "Показать все логи"
    case $routing['showAll']:
        // Извлекаем из запроса параметр: дату, начиная с которой следует выводить логи
        $startDate = $_GET['start_date'];

        // Подключаем класс, отвечающий за работу с бд
        require_once(dirname(__FILE__) . '/Database.php');
        $databaseHandler = new Database($configs['db']);
        $rows = $databaseHandler->getData($startDate);

        // Формируем таблицу результатов запроса
        $dates = array();
        for ($i = 0; $i < 5; $i++){
            $dates[$i] = date('Y-m-d', strtotime($startDate . '+ ' . $i .' day'));
        }

        $table = array();
        foreach($rows as $row) {
            foreach ($dates as $date){
                if (date('Y-m-d', strtotime($row['date_time'])) == $date){
                    $row['date_time'] = date('H:i', strtotime($row['date_time']));
                    if ($row['action_type'] == 'logged in'){
//                    $row['date_time']['late'] = (bool)(date('H:i', strtotime($row['date_time'])) > date('H:i', strtotime('09:15')));
//                        $table[$row['username']][$date] = $row['date_time'];
                        $table[$row['username']][$date]['value'] = $row['date_time'];
                        $table[$row['username']][$date]['late'] = (bool)(date('H:i', strtotime($row['date_time'])) > date('H:i', strtotime('09:15')));
                    } elseif ($row['action_type'] == 'logged out'
//                        && isset($table[$row['username']][$date])){
                        && isset($table[$row['username']][$date]['value'])){
//                        $table[$row['username']][$date] .= ' -- ' . $row['date_time'];
                        $table[$row['username']][$date]['value'] .= ' -- ' . $row['date_time'];
                    }
                }
            }
        }

        include('view.php');

        // Выводим таблицу
        echo "<table border='1'>
            <tr>
            <th>Username</th>";
            for ($i = 0; $i < 5; $i++){
                $dates[$i] = date('Y-m-d', strtotime($startDate . '+ ' . $i .' day'));
                echo "<th>" . $dates[$i] . "</th>";
            }
            echo "</tr>";

        foreach($table as $key => $tableRow) {
            echo "<tr>";
            echo "<td>" . $key . "</td>";
            foreach($tableRow as $rowItem){
                if (isset($rowItem['value'])){
                    if ($rowItem['late']){
                        echo '<td style="color:red;">'.$rowItem['value'].'</td>';
                    } else {
                        echo '<td>'.$rowItem['value'].'</td>';
                    }
                } else {
                    echo '<td>----</td>';
                }
            }
            echo "</tr>";
        }
        echo "</table>";

        break;
    // Действие "Очистить таблицу в БД"
    case $routing['clearDB']:
        // Подключаем класс, отвечающий за работу с бд
        require_once(dirname(__FILE__) . '/Database.php');
        $databaseHandler = new Database($configs['db']);
        if ($result = $databaseHandler->clearLogData() === true){
            echo "DB's table was truncated";
        } else {
            echo $result;
        }

        break;
    // Действие "Распарсить логи Керио и записать данные в базу"
    case $routing['parseLog']:
        // Подключаем парсер логов
        require_once(dirname(__FILE__) . '/Parser.php');
        // Подключаем класс, отвечающий за работу с бд
        require_once(dirname(__FILE__) . '/Database.php');

        $log = file_get_contents($configs['log_file_path']);

        $parser = new Parser();
        $data = $parser->parseString($log);

        $databaseHandler = new Database($configs['db']);
        $databaseHandler->saveParsedData($data);

        break;
}
?>