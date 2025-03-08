<?php

require_once __DIR__ . '/config.php';

$db = mysqli_connect($DB_host, $DB_user, $DB_password, $DB_database);

if(!$db) {
    echo "Error: No se pudo conectar a MySQL.";
    echo "errno de depuración: " . mysqli_connect_errno();
    echo "error de depuración: " . mysqli_connect_error();
    exit;
}