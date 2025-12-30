<?php

namespace Modelos;

use Clases\Database;
use PDOException;

final class Usuario
{
    public private(set) int $cod_usu;

    public int $idUsr {
        get => $this->cod_usu;
    }
    public private(set) string $email;
    public private(set) string $nombre;
    public private(set) string $passwd {
        get => $this->passwd;
    }
    public private(set) string $rol;

    public function __construct(int $newCodigo, string $newEmail, string $newNombre, string $newPasswd, string $newRol)
    {
        $this->cod_usu = $newCodigo;
        $this->email = $newEmail;
        $this->nombre = $newNombre;
        $this->passwd = $newPasswd;
        $this->rol = $newRol;
    }

    public function __clone() {}


    // Métodos

    /**
     * Summary of __toString
     * @return string
     */
    public function __toString(): string
    {
        return "----------------------<br>
            Nombre:     {$this->nombre} <br>
            Email:      {$this->email}<br>
        ";
    }


    /**
     * Summary of verifyPassword
     * @param string $passwordIntroducida
     * @param string $passwdDB
     * @return bool
     * 
     * Verifica que las contraseñas coinciden | Devuelve true o false
     */
    public static function verifyPassword(string $passwordIntroducida, string $passwdDB): bool
    {
        return password_verify($passwordIntroducida, $passwdDB);
    }


    /**
     * Summary of registrarUsuario
     * @param string $newNombre
     * @param string $newEmail
     * @param string $newPasswd
     * @return bool
     * 
     * Esta función inserta un usuario nuevo en la base de datos
     * Si se inserta = true , si no se inserta = false
     */
    public static function registrarUsuario(string $newNombre, string $newEmail, string $newPasswd): bool
    {
        $resultado = false;

        try {

            if (!self::emailUsed($newEmail)) {

                $pdo = Database::conectar();
                $sql = "INSERT INTO Usuario (Nombre, email, Passwd)
                        VALUES (:nombre, :email, :passwd) ;";

                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(":nombre", $newNombre, \PDO::PARAM_STR);
                $stmt->bindValue(":email", $newEmail, \PDO::PARAM_STR);
                $stmt->bindValue(":passwd", $newPasswd, \PDO::PARAM_STR);

                $resultado = $stmt->execute();
            }
        } catch (PDOException $pdoe) {
            die("**ERROR: " . $pdoe->getMessage());
        }

        return $resultado;
    }

    /**
     * Summary of checkEmail
     * @param string $email
     * @return bool
     * 
     * Esta función comprueba si el email ya está siendo usado por otro usuario
     * Si se está usando devuelve true, si no se está usando devuelve false :)
     */
    public static function emailUsed(string $email): bool
    {
        $resultado = false;

        try {

            $pdo = Database::conectar();

            $sql = "SELECT 1 FROM Usuario WHERE email = :email ;";
            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(":email", $email, \PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->fetchColumn() !== false) {

                $resultado = true;
            }
        } catch (PDOException $pdoe) {
            die("**ERROR: " . $pdoe->getMessage());
        }

        return $resultado;
    }

    /**
     * Summary of emailUsedByOther
     * @param mixed $email
     * @param mixed $id
     * @return bool
     * 
     * Comprueba si el email ya se está usando por otro usuario que no sea el usuario pasado por ID
     * Si se está usando = true | Si no se está usando (está libre) = false
     */
    public static function emailUsedByOther($email, $id): bool
    {
        $resultado = false;

        try {

            $pdo = Database::conectar();
            $stmt = $pdo->prepare("SELECT * FROM Usuario WHERE email = ? AND cod_usu != ?");
            $stmt->execute([$email, $id]);
            $fila = $stmt->fetch();

            if ($fila) {
                $resultado = true;
            }

        } catch (PDOException $pdoe) {
            die("**ERROR: " . $pdoe->getMessage());
        }

        return $resultado;
    }


    /**
     * Summary of getById
     * @param int $IDUsuario
     * @return Usuario|null
     * 
     * Devuelve al usuario con ese ID y si no existe devuelve null
     */
    public static function getById(int $IDUsuario): ?Usuario
    {

        $resultado = null;

        try {

            $pdo = Database::conectar();
            $sql = "SELECT * FROM Usuario WHERE cod_usu = :codigo ;";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":codigo", $IDUsuario, \PDO::PARAM_INT);
            $stmt->execute();

            $fila = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($fila) {

                $resultado = new Usuario(
                    $fila["cod_usu"],
                    $fila["email"],
                    $fila["Nombre"],
                    $fila["Passwd"],
                    $fila["rol"]
                );
            }
        } catch (PDOException $pdoe) {
            die("**ERROR: " . $pdoe->getMessage());
        }

        return $resultado;
    }




    /**
     * Summary of getAllNonAdmins
     * @return array
     * 
     * Devuelve una lista con todos los jugadores que NO SON ADMINS
     */
    public static function getAllNonAdmins(): array
    {
        $resultado = [];

        try {

            $pdo = Database::conectar();
            $sql = "SELECT * FROM Usuario WHERE rol != 'admin' ;";
            $stmt = $pdo->query($sql);

            while ($fila = $stmt->fetch(\PDO::FETCH_ASSOC)) {

                $usr = new Usuario(
                    $fila["cod_usu"],
                    $fila["email"],
                    $fila["Nombre"],
                    $fila["Passwd"],
                    $fila["rol"]
                );

                array_push($resultado, $usr);
            }
        } catch (PDOException $pdoe) {
            die("**ERROR: " . $pdoe->getMessage());
        }

        return $resultado;
    }


    /**
     * Summary of getByEmailAndPassword
     * @param string $email
     * @param string $passwd
     * @return Usuario|null
     * 
     * Devuelve el usuario que tiene ese email y esa contraseña si no coinciden devuelve null
     */
    public static function getByEmailAndPassword(string $email, string $passwd): ?Usuario
    {
        $resultado = null;

        try {

            $pdo = Database::conectar();
            $sql = "SELECT * FROM Usuario WHERE email = :email ;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":email", $email,  \PDO::PARAM_STR);
            $stmt->execute();

            $fila = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (($fila) && (self::verifyPassword($passwd, $fila["Passwd"]))) {

                $resultado = new Usuario(
                    $fila["cod_usu"],
                    $fila["email"],
                    $fila["Nombre"],
                    $fila["Passwd"],
                    $fila["rol"]
                );
            }
        } catch (PDOException $pdoe) {
            die("**ERROR: " . $pdoe->getMessage());
        }


        return $resultado;
    }


    /**
     * Summary of updateUser
     * @param int $id
     * @param string $newNombre
     * @param string $newEmail
     * @param string $newPasswd
     * @return bool
     * 
     * Actualiza los datos de un usuario referenciado por ID
     */
    public static function updateUser(int $id, string $newNombre, string $newEmail, string $newPasswd): bool
    {

        try {

            $pdo = Database::conectar();
            $sql = "UPDATE Usuario
            SET Nombre = :nombre,
                email = :email,
                Passwd = :passwd
            WHERE cod_usu = :id
            ;";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":nombre", $newNombre, \PDO::PARAM_STR);
            $stmt->bindValue(":email", $newEmail, \PDO::PARAM_STR);
            $stmt->bindValue(":passwd", $newPasswd, \PDO::PARAM_STR);
            $stmt->bindValue(":id", $id, \PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $pdoe) {
            die("**ERROR" . $pdoe->getMessage());
        }
    }

    /**
     * Summary of deleteUser
     * @param int $id
     * @return bool
     * 
     * Borra un usuario de la base de datos referenciado por un ID
     */
    public static function deleteUser(int $id): bool
    {

        try {

            $pdo = Database::conectar();
            $sql = "DELETE FROM Usuario
                    WHERE cod_usu = :id
                    ;";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":id", $id, \PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $pdoe) {
            die("**ERROR" . $pdoe->getMessage());
        }
    }
}
