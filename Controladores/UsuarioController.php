<?php

namespace Controladores;

use Clases\Auth;
use Clases\Database;
use Clases\Request;
use Dom\CDATASection;
use Modelos\Usuario;
use PDOException;
use Clases\Sesion;

final class UsuarioController extends BaseController
{

    /**
     * Summary of registro
     * @return void
     * 
     * Primero comprueba si hay sesión iniciada, SI la hay, redirige al home y si NO la hay:
     * Para la página de registro.twig, comprueba si es POST y procesa los datos mandados por el formulario
     * Si es GET guarda el error (si lo hay) para mostrarlo y renderiza la página de registro.twig
     */
    public function registro(): void
    {

        if (Sesion::isLogged()) {
            Request::redirect("/");
        }

        if (Request::method("POST")) {
            $this->registrar();
        } else {

            // Si hay algún error se guarda y se borra de la sesión
            $error = $_SESSION["error"] ?? null;
            unset($_SESSION["error"]);

            $this->render("registro.twig", [
                "session" => [
                    "error" => $error
                ]
            ]);
        }
    }

    /**
     * Summary of registrar
     * @return never
     * 
     * Guarda los datos introducidos en el formulario e intenta registrar el usuario
     * Si se registra el usuario se redirige al loguin
     * Si no se registra el usuario se vuelve a renderizar el registro y se muestra el error 
     */
    public function registrar(): never
    {

        $nombre = trim($_POST["nombre"]);
        $email = trim($_POST["email"]);
        $passwd = trim($_POST["passwd"]);

        $hash = password_hash($passwd, PASSWORD_DEFAULT);

        $ok = Usuario::registrarUsuario($nombre, $email, $hash);

        if ($ok) {
            Request::redirect("/login");
        } else {
            $_SESSION["error"] = "El email ya está en uso";
            Request::redirect("/registro");
        }
    }

    /**
     * Summary of login
     * @return void
     * 
     * Se comprueba si hay una sesión iniciada, si la hay redirige a home
     * Si recibe parámetros por POST se llama a la función procesarLogin()
     * Si recibe parámetros por GET renderiza la página de login
     */
    public function login(): void
    {
        if (Sesion::isLogged()) {
            Request::redirect("/home");
        }

        if (Request::method("POST")) {
            $this->procesarLogin();
        } else {

            $error = $_SESSION["error"] ?? null;
            unset($_SESSION["error"]);

            $this->render("login.twig", [
                "session" => [
                    "error" => $error
                ]
            ]);
        }
    }

    /**
     * Summary of procesarLogin
     * @return never
     * 
     * Guarda los datos introducidos en el formulario e intenta iniciar resión
     * Si se inicia sesión se redirige al home
     * Si no se inicia sesión se vuelve a renderizar la página de loguien y se muestra el mensaje de error
     */
    public function procesarLogin(): never
    {
        $email = trim($_POST["email"]);
        $passwd = trim($_POST["passwd"]);

        if (Auth::login($email, $passwd)) {
            Request::redirect("/home");
        } else {
            $_SESSION["error"] = "El correo electrónico o la contraseña son incorrectos";
            Request::redirect("/login");
        }
    }


    /**
     * Summary of soloAdmin
     * @return void
     * 
     * Función para compronar si hay un usuario logueado y comprobar si es admin
     */
    private function soloAdmin(): void
    {
        if (!Sesion::isLogged()) {
            Request::redirect("/");
        }

        $usuario = Usuario::getById(Sesion::getId());
        if ($usuario->rol !== "admin") {
            Request::redirect("/home");
        }
    }

    /**
     * Summary of listar
     * @return void
     * 
     * Renderiza la página para listar todos los usuarios que no son administradores
     */
    public function listar(): void
    {

        $this->soloAdmin();
        $usuarios = Usuario::getAllNonAdmins();
        $this->render("usuarios_listado.twig", [
            "usuarios" => $usuarios
        ]);
    }


    /**
     * Summary of nuevo
     * @return void
     * 
     * Renderiza la página del formulario para la creación de un nuevo usuario
     */
    public function nuevo(): void
    {
        $this->soloAdmin();

        $error = $_GET["error"] ?? null;

        $this->render("usuario_nuevo.twig", [
            "error" => $error
        ]);
    }

    /**
     * Summary of crear
     * @return never
     * 
     * Procesa los datos del formulario para crear un nuevo usuario ( usuario_nuevo.twig )
     */
    public function crear(): never
    {

        $this->soloAdmin();

        $nombre = trim($_POST["nombre"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $password = trim($_POST["password"] ?? "");

        if ($nombre === "" || $email === "" || $password === "") {
            Request::redirect("/admin/usuario/nuevo");
        }

        if (Usuario::emailUsed($email)) {
            Request::redirect("/admin/usuario/nuevo?error=email");
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        Usuario::registrarUsuario($nombre, $email, $hash);
        Request::redirect("/admin/usuarios");
    }


    /**
     * Summary of editar
     * @return void
     * 
     * Renderiza la página del formulario para editar un usuario 
     */
    public function editar(): void
    {

        $this->soloAdmin();

        $id = $_GET["id"] ?? null;

        if (!$id) {
            Request::redirect("/admin/usuarios");
        }

        $usuario = Usuario::getById($id);

        if ($usuario->rol === "admin") {
            Request::redirect("/admin/usuarios");
        }

        $this->render("usuario_editar.twig", [
            "usuario" => $usuario
        ]);
    }


    /**
     * Summary of actualizar
     * @return never
     * 
     * Procesa los datos del formulario para actualizar un usuario en la base de datos ( usuario_editar.twig )
     */
    public function actualizar()
    {
        $this->soloAdmin();

        $id = $_POST["id"] ?? null;
        $nombre = trim($_POST["nombre"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $password = trim($_POST["password"] ?? "");

        if (!$id || $nombre === "" || $email === "") {
            Request::redirect("/admin/usuarios");
        }

        $usuario = Usuario::getById($id);

        if ($usuario->rol === "admin") {
            Request::redirect("/admin/usuarios");
        }

        if (Usuario::emailUsedByOther($email, $id)) {
            Request::redirect("/admin/usuario/editar/$id?error=email");
        }

        if ($password !== "") {
            $hash = password_hash($password, PASSWORD_DEFAULT);
        } else {
            $hash = $usuario->passwd;
        }


        Usuario::updateUser($id, $nombre, $email, $hash);
        Request::redirect("/admin/usuarios");
    }

    /**
     * Summary of eliminar
     * @return never
     * 
     * Funcionalidad para eliminar a un usuario de la base de datos
     */
    public function eliminar()
    {
        $this->soloAdmin();

        $id = $_GET["id"] ?? null;

        if (!$id) {
            Request::redirect("/admin/usuarios");
        }
        $usuario = Usuario::getById($id);

        if (!$usuario) {
            Request::redirect("/admin/usuarios");
        }

        if ($usuario->rol === "admin") {
            Request::redirect("/admin/usuarios");
        }

        Usuario::deleteUser($id);
        Request::redirect("/admin/usuarios");


    }




}
