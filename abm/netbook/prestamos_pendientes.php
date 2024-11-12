<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

include "../template/header.php";
include '../funciones.php';
include '../../config/db.php';

// Verifica que la sesión de usuario esté definida
if (!isset($_SESSION['user_area'])) {
    die(json_encode(['error' => 'Área de usuario no definida.']));
}

$user_area = $_SESSION['user_area'];
$conexion = conexion();

if (!isset($_SESSION['user_area'])) {
    die(json_encode(['error' => 'Área de usuario no definida.']));
}

try {
    // Consulta para contar los registros con 'Pending' en el área específica del usuario
    $stmt = $conexion->prepare("
        SELECT COUNT(*) AS count
        FROM registros 
        INNER JOIN recurso ON recurso.recurso_id = registros.idrecurso 
        INNER JOIN users ON registros.idusuario = users.user_id 
        INNER JOIN tipo_recurso ON recurso.recurso_tipo = tipo_recurso.tipo_recurso_id 
        INNER JOIN area ON tipo_recurso.tipo_recurso_area = area.id 
        WHERE registros.opcion = 'Pending' 
        AND area.id = :user_area
    ");

    // Ejecuta la consulta
    $stmt->execute([':user_area' => $user_area]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Retorna el conteo en formato JSON
    error_log(print_r($result, true));
    echo json_encode(['count' => $result['count']]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}





