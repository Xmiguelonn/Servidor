<?php

namespace Controladores;


abstract class BaseController{
    
    protected \Twig\Environment $twig;

    public function __construct()
    {
        require_once "./vendor/autoload.php" ;

        $loader = new \Twig\Loader\FilesystemLoader("./vistas") ;
        
        $this->twig = new \Twig\Environment($loader) ;

    }

    /**
     * Summary of render
     * @param string $vista
     * @param array $args
     * @return void
     * 
     * Función que se encarga de renderizar todas las vidtas
     */
    public function render(string $vista, array $args = []): void
		{
			echo $this->twig->render($vista, $args) ;
		}


}