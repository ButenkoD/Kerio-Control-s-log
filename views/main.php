<html>
<head>
    <script type="application/javascript" src="../static/js/jquery-1.11.2.min.js"></script>
    <script type="application/javascript" src="../static/js/jquery-ui-1.11.2.custom/jquery-ui.min.js"></script>
    <script type="application/javascript" src="../static/js/jquery.dataTables.min.js"></script>
    <script type="application/javascript" src="../static/bootstrap3/dist/js/bootstrap.min.js"></script>
    <!--    <script type="application/javascript" src="../static/js/preLoader.js"></script>-->
    <link rel="stylesheet" href="../static/js/jquery-ui-1.11.2.custom/jquery-ui.min.css">
    <link rel="stylesheet" href="../static/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../static/css/jquery.dataTables_themeroller.css">
    <link rel="stylesheet" href="../static/bootstrap3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../static/css/kerio.css">

</head>
<body class="container">
<div class="container">
    <form>
        <div class="top-block">
            <label>Start date</label>
            <input type="text" id="start-date" name="date" value="2014-12-05">
            <!--onclick="makeRequest(event, 'showAll');-->
            <!--    <button class="button btn btn-default" onclick="makeRequest(event, 'showAll');">Show log records</button>-->
            <button class="button btn btn-warning" onclick="makeRequest(event,'clearDB');">Clear Database</button>
            <button class="button btn btn-default" onclick="makeRequest(event,'parseLog');">Parse Kerio log file
            </button>
            <button class="button btn btn-default" onclick="makeRequest(event, 'showAll');">Show log records</button>
            <input type="radio" name="log-source" value="file" checked/> Use single file
            <input type="radio" name="log-source" value="dir"/> Use directory
        </div>
    </form>
    <img id="loader" src="../static/images/loader.gif" alt="" width="50" height="53"/>
</div>
<div id="txtHint" class="container"><b>Required info will be listed here.</b></div>
<script>
    function makeRequest(event, action) {
        $("#loader").css("display", "block");
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
                    $("#dataTable").addClass("table table-hover table-bordered");
                }
            }
        }
        $("#loader").css("display", "none");
        var start_date = document.getElementById("start-date").value;
        xmlhttp.open("GET", "index.php?request_action=" + action + "&log-source=" + $(":radio:checked").val() + "&start_date=" + start_date, true);
        xmlhttp.send();
    }

    $(document).ready(function () {
        $('.button').button();
        $(':input').addClass("ui-widget ui-widget-content ui-corner-all");
        $('label, #txtHint').addClass("ui-widget  label ");
        $('#start-date').datepicker({dateFormat: 'yy-mm-dd'});
    })

</script>

</body>
</html>