<?php

namespace MVC;

class Router {

    public array $getRoutes = [];
    public array $postRoutes = [];

    public function get($url, $fn) {
        $this->getRoutes[$url] = $fn;
    }

    public function post($url, $fn) {
        $this->postRoutes[$url] = $fn;
    }

    public function comprobarRutas() {

        // Proteccion de rutas
        session_start();

        // Arreglo de rutas protegidas

        // Obtencion de la URL y el metodo
        $currentURL = $_SERVER['PATH_INFO'] ?? null;
        $method = $_SERVER['REQUEST_METHOD'];

        // Verificacion de ruta actual con las del proyecto
        if($method === 'GET') {
            $fn = $this->getRoutes[$currentURL] ?? null;
        } else {
            $fn = $this->postRoutes[$currentURL] ?? null;
        }

        // Obtencion de los argumentos de la funcione si es que existe
        if($fn) {
            // Call user fn va a llamar una función cuando no sabemos cual sera y le pasara los argumentos a this
            call_user_func($fn, $this);
        } else {
            echo "Página No Encontrada o Ruta no válida";
        }
    }
}