<?php
require_once "autoload.php";

use Clases\Sesion;
use Clases\Request;
use Clases\Auth;

$modelo = $_GET["modelo"] ?? "usuario";
$metodo = $_GET["metodo"] ?? "registro";

# Contruimos el nombre del controlador a partir del modelo
$nombreControlador = ucfirst("{$modelo}Controller");

# Construimos el nombre de la clase
$nombreClase = "Controladores\\{$nombreControlador}";

# Instanciamos la clase
$controlador = new $nombreClase;

# Invocamos el método solicitado
$controlador->$metodo();
