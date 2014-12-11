<?php
$q = $_GET['q'];

$routing = array(
    'showAll' => 'showAll',
    'clearDB' => 'clearDB',
    'parseLog'=> 'parseLog'
);

switch($q){
    // Действие "Показать все логи"
    case $routing['showAll']:
        $startDate = $_GET['start_date'];
        // Подключаем класс, отвечающий за работу с бд
        require_once(dirname(__FILE__) . '/Database.php');
        $databaseHandler = new Database();
        $rows = $databaseHandler->getData();

        // Формируем таблицу результатов запроса
        for ($i = 0; $i < 5; $i++){
            $dates[$i] = date('Y-m-d', strtotime($startDate . '+ ' . $i .' day'));
        }
        $table = array();
        foreach($rows as $row) {
            foreach ($dates as $date){
                if (date('Y-m-d', strtotime($row['date_time'])) == $date){
                    $row['date_time'] = date('H:i', strtotime($row['date_time']));
                    if ($row['action_type'] == 'logged in'){
                        $table[$row['username']][$date] = $row['date_time'];
                    } elseif ($row['action_type'] == 'logged out'
                        && isset($table[$row['username']][$date])){
                        $table[$row['username']][$date] .= ' -- ' . $row['date_time'];
                    }
                }
            }
        }

        // Выводим таблицу
        echo "<table border='1'>
            <tr>
            <th>Username</th>";
            for ($i = 0; $i < 5; $i++){
                $dates[$i] = date('Y-m-d', strtotime($startDate . '+ ' . $i .' day'));
                echo "<th>" . $dates[$i] . "</th>";
            }
//            echo "<th>Time</th>
            echo "</tr>";

        foreach($table as $key => $tableRow) {
            echo "<tr>";
            echo "<td>" . $key . "</td>";
            foreach($tableRow as $rowItem){
                echo "<td>";
                echo isset($rowItem) ? $rowItem : '----';
                echo "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";

        break;
    // Действие "Очистить таблицу в БД"
    case $routing['clearDB']:
        // Подключаем класс, отвечающий за работу с бд
        require_once(dirname(__FILE__) . '/Database.php');
        $databaseHandler = new Database();
        if ($result = $databaseHandler->clearLogData() === true){
            echo "DB's table was truncated";
        } else {
            echo $result;
        }

        break;
    // Действие "Распарсить логи Керио и записать данные в базу" @todo реализовать
    case $routing['parseLog']:
        break;
}
?>