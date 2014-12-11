<?php
$q = $_GET['q'];
$startDate = $_GET['start_date'];

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
        var_dump($rows);die();

        // Формируем таблицу с результатов запроса
        $dates = array();
        echo "<table border='1'>
            <tr>
            <th>Username</th>";
            for ($i = 0; $i < 5; $i++){
                $dates[$i] = date('Y-m-d', strtotime($startDate . '+ ' . $i .' day'));
                echo "<th>" . $dates[$i] . "</th>";
            }
            echo "<th>Time</th>
            </tr>";

        foreach($rows as $key => $row) {
            echo "<tr>";
            echo "<td>" . $row['username'] . "</td>";
            echo "<td>";
                if (date('Y-m-d', strtotime($row['date_time']))){
                    echo date('Y-m-d', strtotime($row['date_time']));
                }
            echo "</td>";
            echo "<td>" . $row['date_time'] . "</td>";
            echo "</tr>";
        }
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