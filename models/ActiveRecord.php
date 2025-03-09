<?php

namespace Model;

class ActiveRecord {

    // ─── Base De Datos ───────────────────────────────────────────────────
    protected static $db;
    protected static $tabla = '';
    protected static $columnasDB = [];

    // ─── Alertas Y Mensajes ──────────────────────────────────────────────
    protected static $alertas = [];

    // ─── Definir La Conexión A La DB - includes/database.php ────────────────
    public static function setDB($database) {
        self::$db = $database;
    }
}