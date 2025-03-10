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

    // ─── Operaciones CRUD ──────────────────────────────────────────────────

    // Verificación de crar o actualizar
    public function guardar() {
        $resultado = '';

        if(!is_null($this->id)) {
            // Actualizar
            $resultado = $this->actualizar();
        } else {
            // Crear
            $resultado = $this->crear();
        }

        return $resultado;
    }

    // Consultas de lectura (read)
    // Obtener todos los registros
    public function all() {
        $query = " SELECT * FROM " . static::$tabla;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Buscar un registro por su ID
    public function find($id) {
        $query = " SELECT * FROM " . static::$tabla . " WHERE id = {$id}";
        $resultado = self::consultarSQL($query);
        return  array_shift($resultado);
    }

    // Obtener Registros con cierta cantidad
    public function get($limite) {
        $query = " SELECT * FROM " . static::$tabla . " LIMIT {$limite}";
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado ) ;
    }

    // Consultas create, update y delete

    public function crear() {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        // Insertar en la base de datos
        $query = " INSERT INTO " . static::$tabla . " ( ";
        $query .= join(', ', array_keys($atributos));
        $query .= " ) VALUES (' "; 
        $query .= join("', '", array_values($atributos));
        $query .= " ') ";

        // Resultado de la consulta
        $resultado = self::$db->query($query);
        return [
        'resultado' =>  $resultado,
        'id' => self::$db->insert_id
        ];
    }

    public function actualizar() {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        // Iterar para ir agregando cada campo de la BD
        $valores = [];
        foreach($atributos as $key => $value) {
            $valores[] = "{$key}='{$value}'";
        }

        // Consulta SQL
        $query = "UPDATE " . static::$tabla ." SET ";
        $query .=  join(', ', $valores );
        $query .= " WHERE id = '" . self::$db->escape_string($this->id) . "' ";
        $query .= " LIMIT 1 "; 

        // Actualizar BD
        $resultado = self::$db->query($query);
        return $resultado;
    }

    public function eliminar() {
        $query = "DELETE FROM "  . static::$tabla . " WHERE id = " . self::$db->escape_string($this->id) . " LIMIT 1";
        $resultado = self::$db->query($query);
        return $resultado;
    }
}