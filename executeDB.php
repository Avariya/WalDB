<?php
/**
 * Created by PhpStorm.
 * User: avariya
 * Date: 8/10/15
 * Time: 1:41 PM
 */

include 'wallDB.php';

$conf = array('wal_path' => '/tmp/release_wall', 'db_file' => '/tmp/release_db');
$db = new \WalDB\wallDB($conf);

set_time_limit(0);
ob_implicit_flush();

function proceedInput($buf, \WalDB\wallDB $db)
{
    $toDo = explode(';', trim($buf));
    switch ($toDo[0]) {
        case 0://remove
            if (strlen($toDo[1])) {
                $answer = $db->delete($toDo[1]);
            } else {
                $answer = false;
            }
            break;
        case 1://update
            if (strlen($toDo[1]) && strlen($toDo[2])) {
                $answer = $db->update($toDo[1], $toDo[2]);
            } else {
                $answer = false;
            }
            break;
        case 2://insert
            if (strlen($toDo[1])) {
                $answer = $db->insert($toDo[1]);
            } else {
                $answer = false;
            }
            break;
        case 3://show all
            ob_start();
            $db->showAll();
            return ob_get_flush();
        default:
            $answer = false;
    }

    if ($answer) {
        $talkback = 'success';
    } else {
        $talkback = 'error';
    }
    return $talkback;
}

$host = '127.0.0.1';
$port = 3443;

$socket = socket_create(AF_INET, SOCK_STREAM, 0);
$result = socket_bind($socket, $host, $port);
$result = socket_listen($socket, 3);

do {
    $spawn = socket_accept($socket) or die("Could not accept incoming connection\n");
    do {
        $buf = @socket_read($spawn, 1024);
        if (false !== $buf) {
            $buf = trim($buf);

            $output = proceedInput($buf, $db);
            socket_write($spawn, $output, strlen($output)) or socket_close($spawn);
        } else {
            break;
        }

    } while (true);
} while (true);


socket_close($socket);

