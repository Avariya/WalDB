<?php
/**
 * Created by PhpStorm.
 * User: avariya
 * Date: 8/10/15
 * Time: 1:41 PM
 */

include 'wallDB.php';

$conf = array('wal_path'=>'/tmp/release_wall','db_file'=>'/tmp/release_db');
$db = new \WalDB\wallDB($conf);

set_time_limit(0);
ob_implicit_flush();
$address = '127.0.0.1';
$port = 3443;

if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
    echo "Не удалось выполнить socket_create(): причина: " . socket_strerror(socket_last_error()) . "\n";
}

if (socket_bind($sock, $address, $port) === false) {
    echo "Не удалось выполнить socket_bind(): причина: " . socket_strerror(socket_last_error($sock)) . "\n";
}

if (socket_listen($sock, 5) === false) {
    echo "Не удалось выполнить socket_listen(): причина: " . socket_strerror(socket_last_error($sock)) . "\n";
}

do {
    if (($msgsock = socket_accept($sock)) === false) {
        echo "Не удалось выполнить socket_accept(): причина: " . socket_strerror(socket_last_error($sock)) . "\n";
        break;
    }
    /* Отправляем инструкции. */
    $msg = "\nДобро пожаловать на тестовый сервер PHP. \n" .
        "Чтобы отключиться, наберите 'выход'. Чтобы выключить сервер, наберите 'выключение'.\n";
    socket_write($msgsock, $msg, strlen($msg));

    do {
        if (false === ($buf = socket_read($msgsock, 2048, PHP_NORMAL_READ))) {
            echo "Не удалось выполнить socket_read(): причина: " . socket_strerror(socket_last_error($msgsock)) . "\n";
            break 2;
        }
        if (!$buf = trim($buf)) {
            continue;
        }


        //Socket realization to listen my DB

        //parse line
        $toDo = explode(';',trim($buf));
        switch ($toDo[0]) {
            case 0://remove
                $answer = $db->delete($toDo[1]);
                break;
            case 1://update
                $answer = $db->update($toDo[1], $toDo[2]);
                break;
            case 2://insert
                $answer = $db->insert($toDo[1]);
                break;
            default:
                $answer = false;
        }

        //End socket realization
        if ($answer) {
            $talkback = 'success';
        } else {
            $talkback = 'error';
        }

        socket_write($msgsock, $talkback, strlen($talkback));

        echo "$buf\n";
    } while (true);
    socket_close($msgsock);
} while (true);

socket_close($sock);
