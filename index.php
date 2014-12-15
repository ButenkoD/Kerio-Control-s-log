<?php
/**
 * Created by PhpStorm.
 * User: apedan
 * Date: 12.12.14
 * Time: 13:12
 */
?>
<html>
<head>
</head>
<body>
<form>
    <label>Start date</label>
    <input type="text" id="start-date" name="date" value="2014-12-05">
    <button onclick="makeRequest('showAll');">Show log records</button>
    <button onclick="makeRequest('clearDB');">Clear Database</button>
    <button onclick="makeRequest('parseLog');">Parse Kerio log file</button>
</form>
<br>
<div id="txtHint"><b>Required info will be listed here.</b></div>
<script>
    function makeRequest(action) {
        event.preventDefault();

        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("txtHint").innerHTML = xmlhttp.responseText;
            }
        }
        var start_date = document.getElementById("start-date").value;
        xmlhttp.open("GET", "front-controller.php?request_action=" + action + "&start_date=" + start_date, true);
        xmlhttp.send();
    }
</script>
</body>
</html>