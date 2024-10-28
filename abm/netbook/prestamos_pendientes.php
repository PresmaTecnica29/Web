<?php
include "../template/header.php";
include '../funciones.php';


csrf();
if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  die();
}

$user_area = $_SESSION['user_area'];
$error = false;
$config = include('../../config/db.php');


// Consulta para contar préstamos solicitados y no confirmados
$stmt = $pdo->prepare("SELECT COUNT(*)
    FROM 
      registros 
    INNER JOIN 
      recurso ON recurso.recurso_id = registros.idrecurso 
    INNER JOIN 
      users ON registros.idusuario = users.user_id 
    INNER JOIN 
      tipo_recurso ON recurso.recurso_tipo = tipo_recurso.tipo_recurso_id 
    INNER JOIN 
      area ON tipo_recurso.tipo_recurso_area = area.id 
      INNER JOIN 
  horario ON horario.id = registros.fin_prestamo
    WHERE 
      registros.devuelto = 'Pending' 
      AND area.id = :user_area");
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);


// Retorna el conteo en formato JSON
echo json_encode(['count' => $result['count']]);
