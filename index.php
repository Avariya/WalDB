<html>
<head>
<title>
    DB Manager
</title>
    <script type="text/javascript">
        function changeInputs(sel){
            var e = document.getElementsByName("val2")[0];
            if (sel.options[sel.selectedIndex].value == 1){
                e.removeAttribute('disabled');
            } else {
                e.setAttribute('disabled','disabled');
            }
        }
    </script>
</head>
<body>

<?php

function executeAction($string){
    $host = '127.0.0.1';
    $port = 3443;

    $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
    $result = socket_connect($socket, $host, $port) or die("Could not connect to server\n");
    socket_write($socket, $string, strlen($string)) or die("Could not send data to server\n");
    $result = socket_read ($socket, 1024) or die("Could not read server response\n");

    socket_close($socket);
    return $result;
}

if (count($_POST) > 0){
    $action = '';
    if ($_POST['action'] != '1'){
        $action = $_POST['action'].';'.$_POST['val1'];
    } else {
        $action = $_POST['action'].';'.$_POST['val1'].';'.$_POST['val2'];
    }
    echo $action,'<br />','Answer:',executeAction($action);
}
?>


<form action="index.php" method="post">
    <select name="action" onchange="changeInputs(this)">
        <option value="2" selected>INSERT</option>
        <option value="1">UPDATE</option>
        <option value="0">REMOVE</option>
    </select>
    <input type="text" name="val1" />
    <input type="text" name="val2" disabled="disabled" />
    <input type="submit" value="Action!" />
</form>


</body>
</html>




