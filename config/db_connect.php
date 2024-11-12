<?php
$db_host = 'localhost';
$db_user = 'wino';
$db_pass = 'Mbc22016';
$db_name = 'vote_system';

try {
    $db = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($db->connect_error) {
        throw new Exception("Connection failed: " . $db->connect_error);
    }
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}
?> 