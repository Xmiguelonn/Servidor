<?php

spl_autoload_register(function ($clase) {

    // Convertir namespace a ruta
    $ruta = __DIR__ . '/' . str_replace('\\', '/', $clase) . '.php';

    if (file_exists($ruta)) {
        require_once $ruta;
    }
});

// Iniciar sesión automáticamente
\Clases\Sesion::start();
