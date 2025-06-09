<?php

class Conexion extends PDO
{
  private $tipo_de_base = 'sqlsrv';
  private $host = '172.17.1.20';
  private $nombre_de_base = 'EdocenteV2';
  private $usuario = 'sergioO';
  private $contrasena = 'Serch1319';
  private static $instancia = null;

  public function __construct()
  {
    try {
      // Crear DSN para conexión SQL Server
      $dsn = "{$this->tipo_de_base}:Server={$this->host};Database={$this->nombre_de_base};Encrypt=0;TrustServerCertificate=1";

      // Crear la conexión con PDO
      self::$instancia = new PDO($dsn, $this->usuario, $this->contrasena);

      // Configurar el charset, formato de fecha y lenguaje
      // self::$instancia->exec("SET NAMES 'utf8'");
      self::$instancia->exec("SET DATEFORMAT DMY; SET LANGUAGE spanish;");
    } catch (PDOException $e) {
      echo 'Ha surgido un error y no se puede conectar a la base de datos. Detalle: ' . $e->getMessage();
      exit;
    }
  }

  public static function getInstancia()
  {
    if (!self::$instancia) {
      new self();
    }
    return self::$instancia;
  }

  public static function cerrar()
  {
    self::$instancia = null;
  }
}
