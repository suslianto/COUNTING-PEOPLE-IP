<?php
// api/index.php

header("Content-Type: application/json");
error_reporting(E_ALL);
date_default_timezone_set("Asia/Jakarta");

// Routing via PATH_INFO
$path = $_SERVER['PATH_INFO'] ?? '/';

switch ($path) {
    case '/get_data':
        require __DIR__ . '/modules/get_data.php';
        break;
    case '/blacklist':
        require __DIR__ . '/modules/blacklist.php';
        break;
    case '/total':
        require __DIR__ . '/modules/total.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(["error" => "Route not found"]);
        break;
}
?>
