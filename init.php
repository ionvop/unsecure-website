<?php

if (php_sapi_name() != "cli") {
    echo "Please run this script from the command line.";
    exit;
}

$db = new SQLite3("database.db");
$query = file_get_contents("init.sql");
$db->exec($query);
echo "Database initialized.";