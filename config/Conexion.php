<?php

class Conexion
{
    private static ?Conexion $instancia = null;
    private PDO $pdo;

    // Datos de configuración de la base de datos
    private string $host   = "localhost";
    private string $bd     = "ediciones_fares";
    private string $usuario = "root";
    private string $clave   = "";

    // Constructor privado: evita crear instancias desde fuera de la clase
    private function __construct()
    {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->bd};charset=utf8mb4";
            $this->pdo = new PDO($dsn, $this->usuario, $this->clave);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }

    // Devuelve la única instancia de conexión (patrón Singleton)
    public static function obtenerConexion(): PDO
    {
        if (self::$instancia === null) {
            self::$instancia = new Conexion();
        }
        return self::$instancia->pdo;
    }
}
