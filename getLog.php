<?php

// Подключаем класс, отвечающий за работу с бд
require_once(dirname(__FILE__) . '/Database.php');

//$q = intval($_GET['q']);

//$con = mysqli_connect('localhost','peter','abc123','my_db');
//if (!$con) {
//    die('Could not connect: ' . mysqli_error($con));
//}
//
//mysqli_select_db($con,"ajax_demo");
//$sql="SELECT * FROM user WHERE id = '".$q."'";
//$result = mysqli_query($con,$sql);

$databaseHandler = new Database();
$rows = $databaseHandler->getData();

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
//
//mysqli_close($con);

?>