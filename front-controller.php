<?php
$q = $_GET['q'];

$routing = array(
    'showAll' => 'showAll',
    'clearDB' => 'clearDB',
);

switch($q){
    // Действие "Показать все логи"
    case $routing['showAll']:
        // Подключаем класс, отвечающий за работу с бд
        require_once(dirname(__FILE__) . '/Database.php');
        $databaseHandler = new Database();
        $rows = $databaseHandler->getData();
        // Формируем таблицу с результатов запроса
        echo "<table border='1'>
            <tr>
            <th>Username</th>
            <th>Lo</th>
            <th>Time</th>
            </tr>";

        foreach($rows as $row) {
            echo "<tr>";
            echo "<td>" . $row['username'] . "</td>";
            echo "<td>" . $row['action_type'] . "</td>";
            echo "<td>" . $row['date_time'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";

        break;
    case $routing['clear']:
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