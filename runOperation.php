<?php
/**
 * Created by PhpStorm.
 * User: avariya
 * Date: 8/10/15
 * Time: 11:36 AM
 */
include 'Database.php';

use WalDB\SimpleDB;

$db = new SimpleDB();

$db->showAll();

$index = $db->insert(10);

$db->showAll($db);

$db->update($index,25);

$db->showAll();

$db->insert(35);

$db->showAll();