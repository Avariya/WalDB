<?php
/**
 * Created by PhpStorm.
 * User: avariya
 * Date: 8/10/15
 * Time: 1:52 PM
 */

$host = '127.0.0.1';
$port = 3443;

$conf = array('wal_path'=>'/tmp/release_wall','db_file'=>'/tmp/release_db');

$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
// connect to server
$result = socket_connect($socket, $host, $port) or die("Could not connect to server\n");
// send string to server
$message = '3';
socket_write($socket, $message, strlen($message)) or die("Could not send data to server\n");
// get server response
$result = socket_read ($socket, 1024) or die("Could not read server response\n");

socket_close($socket);

echo "Current DB state: ".PHP_EOL.$result.PHP_EOL;

echo "Physical DB file: ".PHP_EOL.file_get_contents($conf['db_file']).PHP_EOL;

echo "Physical log file: ".PHP_EOL.file_get_contents($conf['wal_path']).PHP_EOL;

// close socket
