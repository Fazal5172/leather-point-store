<?php

require_once __DIR__ . "/app.php";
require_once __DIR__ . "/Database.php";
require_once __DIR__ . "/developer.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>