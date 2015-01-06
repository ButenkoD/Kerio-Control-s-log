<html>
<head>
    <script type="application/javascript" src="../static/js/jquery-1.11.2.min.js"></script>
    <script type="application/javascript" src="../static/js/jquery-ui-1.11.2.custom/jquery-ui.min.js"></script>
    <script type="application/javascript" src="../static/js/jquery.dataTables.min.js"></script>

    <link rel="stylesheet" href="../static/js/jquery-ui-1.11.2.custom/jquery-ui.min.css">
    <link rel="stylesheet" href="../static/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../static/css/jquery.dataTables_themeroller.css">
    <link rel="stylesheet" href="../static/css/kerio.css">
</head>
<body>
<form>
    <label>Start date</label>
    <input type="text" id="start-date" name="date" value="2014-12-05">
    <!--onclick="makeRequest(event, 'showAll');-->
    <button class="button" onclick="makeRequest(event, 'showAll');">Show log records</button>
    <button class=" button" onclick="makeRequest(event,'clearDB');">Clear Database</button>
    <button class="button" onclick="makeRequest(event,'parseLog');">Parse Kerio log file</button>
</form>

<br>

<div id="txtHint"><b>Required info will be listed here.</b></div>
<script>
    function makeRequest(event, action) {
        event.preventDefault();

        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("txtHint").innerHTML = xmlhttp.responseText;
                if (action == 'showAll') {
                    $("#dataTable").dataTable({
                        aLengthMenu: [
                            [25, 50, 75, -1],
                            [25, 50, 75, "All"]
                        ],
                        iDisplayLength: -1
                    });
                }
            }
        }
        var start_date = document.getElementById("start-date").value;
        xmlhttp.open("GET", "index.php?request_action=" + action + "&start_date=" + start_date, true);
        xmlhttp.send();
    }

    $(document).ready(function () {
        $('.button').button();
        $(':input').addClass("ui-widget ui-widget-content ui-corner-all");
        $('label, #txtHint').addClass("ui-widget  label");
        $('#start-date').datepicker({dateFormat: 'yy-mm-dd'});
    })


</script>
</body>
</html>