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

    // ─── Alertas asignar, obtener y validar ───────────────────────────────────────────────
    public static function setAlerta($tipo, $mensaje) {
        static::$alertas[$tipo][] = $mensaje;
    }

    public static function getAlerta() {
        return static::$alertas; 
    }

    public static function validar() {
        static::$alertas = [];
        return static::$alertas;
    }

    // ─── Consulta SQL Para Crear Un Objeto En Memoria ────────────────────
    public static function consultarSQL($query) {
        // Consultar la base de datos
        $resultado = static::$db->query($query);

        // Iterar los resultados
        $array = [];
        while($registro = $resultado->fetch_assoc()) {
            $array[] = static::crearObjeto($registro);
        }

        // Liberar memoria
        $resultado->free();

        // Retorno de resultados
        return $array;
    }

    // Creacion del objeto para cada fila del registro de la DB
    public static function crearObjeto($registro) {
        $objeto = new static;

        foreach($registro as $key => $value) {
            if(property_exists($objeto, $value)) {
                $objeto->$key = $value;
            }
        }

        return $objeto;
    }

    // Identificar y unir los atributos de la BD
    public function atributos() {
        $atributos = [];

        foreach(static::$columnasDB as $columna) {
            if($columna === 'id') continue;
            $atributos[$columna] = $this->$columna;
        }

        return $atributos;
    }

    // Sanitizar los datos antes de guardarlos en la BD
    public function sanitizarAtributos() {
        $atributos = $this->atributos();
        $sanitizado = [];

        foreach($atributos as $key => $value) {
            $sanitizado[$key] = self::$db->scape_string($value);
        }

        return $sanitizado;
    }

    // Sincroniza BD con Objetos en memoria
    public function sincronizar($args = []) {
        foreach($args as $key => $value) {
            if(property_exists($this, $key) && !is_null($value)) {
                $this->$key = $value;
            }
        }
    }
}