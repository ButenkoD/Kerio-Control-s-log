<?php
$q = $_GET['q'];

$routing = array(
    'showAll' => 'showAll',
    'clearDB' => 'clearDB',
);

switch($q){
    // Действие "Показать все логи"
    case $routing['showAll']:
        $startDate = $_GET['start_date'];
        // Подключаем класс, отвечающий за работу с бд
        require_once(dirname(__FILE__) . '/Database.php');
        $databaseHandler = new Database();
        $rows = $databaseHandler->getData();

        for ($i = 0; $i < 5; $i++){
            $dates[$i] = date('Y-m-d', strtotime($startDate . '+ ' . $i .' day'));
        }
        $table = array();
        foreach($rows as $row) {
            foreach ($dates as $date){
//                var_dump(date('Y-m-d', strtotime($row['date_time'])));
                if (date('Y-m-d', strtotime($row['date_time'])) == $date
                && $row['action_type'] == 'logged in'){
                    $table[$row['username']][$date] = $row['date_time'];
                }
            }
        }

        // Формируем таблицу с результатов запроса
        $dates = array();
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

//        foreach($rows as $key => $row) {
//            echo "<tr>";
//            echo "<td>" . $row['username'] . "</td>";
//            echo "<td>";
//                if (date('Y-m-d', strtotime($row['date_time']))){
//                    echo date('Y-m-d', strtotime($row['date_time']));
//                }
//            echo "</td>";
//            echo "<td>" . $row['date_time'] . "</td>";
//            echo "</tr>";
//        }
        echo "</table>";

        break;
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
}

//$q = intval($_GET['q']);

//$con = mysqli_connect('localhost','peter','abc123','my_db');
//if (!$con) {
//    die('Could not connect: ' . mysqli_error($con));
//}
//
//mysqli_select_db($con,"ajax_demo");
//$sql="SELECT * FROM user WHERE id = '".$q."'";
//$result = mysqli_query($con,$sql);

//
//mysqli_close($con);

?>