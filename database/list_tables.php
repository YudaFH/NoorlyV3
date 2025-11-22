<?php
$db = new SQLite3(__DIR__ . '/database.sqlite');
$res = $db->query("SELECT name FROM sqlite_master WHERE type='table'");
while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
    echo $row['name'] . PHP_EOL;
}
