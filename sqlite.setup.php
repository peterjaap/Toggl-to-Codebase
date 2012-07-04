<?php
$setup = false;
if(!file_exists('timetrack.sqlite')) {
    $setup = true;
}

$db = new DB("timetrack.sqlite");

if($setup) {
    $db->exec("CREATE TABLE entries (
        `id` INTEGER PRIMARY KEY AUTOINCREMENT,
        `minutes` INTEGER,
        `c-ticket-id` INTEGER,
        `c-time-session-id` INTEGER,
        `t-entry-id` INTEGER,
        `summary` LONGTEXT,
        `added` date
    );");
}