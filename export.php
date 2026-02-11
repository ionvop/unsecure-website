<?php

if (php_sapi_name() != "cli") {
    echo "Please run this script from the command line.";
    exit;
}

$db = new SQLite3("database.db");

$query = <<<SQL
    SELECT `sql`
    FROM `sqlite_master`
    WHERE `type` = 'table'
    AND `name` NOT LIKE 'sqlite_%'
SQL;

$tables = $db->query($query);
$result = "";

while ($table = $tables->fetchArray()) {
    $result .= "{$table['sql']};\n";
}

file_put_contents("init.sql", $result);
echo "Database exported.";