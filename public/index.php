<?php

$router = require_once __DIR__ . '/../bootstrap/app.php';

// Match and execute
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
