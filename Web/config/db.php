<?php


define("DB_HOST", "190.228.29.62");
define("DB_NAME", "bdwebet29");
define("DB_USER", "bdwebet29");
define("DB_PASS", "Tecnica29!");
define("DB_CHARSET", "utf8mb4");
function conexion()
{
  try {

    $con = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET, DB_USER, DB_PASS, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false
    ]);
    return $con;
  } catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    return false;
  }
}
return [
  'db' => [
    'host' => DB_HOST,
    'user' => DB_USER,
    'pass' => DB_PASS,
    'name' => DB_NAME,
    'options' => [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false
    ]
  ]
];
