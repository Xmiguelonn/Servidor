<?php

error_reporting(E_ALL & ~E_WARNING);

spl_autoload_extensions(".php");
spl_autoload_register();


\Clases\Sesion::start();