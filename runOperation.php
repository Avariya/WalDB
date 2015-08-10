<?php
/**
 * Created by PhpStorm.
 * User: avariya
 * Date: 8/10/15
 * Time: 11:36 AM
 */
namespace walDB;

include 'wallDB.php';

$conf = array('wal_path'=>'/tmp/wall','db_file'=>'/tmp/db');

$db = new wallDB($conf);

$db->showAll();

$index = $db->insert(10);

$db->showAll($db);

$db->update($index,25);

$db->showAll();

$db->insert(35);

$db->showAll();