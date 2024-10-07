<?php
$config = include('../../config/db.php');
$conexion = conexion();

if (isset($_POST['tipo_recurso_id'])) {
    $tipoRecursoId = $_POST['tipo_recurso_id'];

    $stmt = $conexion->prepare("
        SELECT recurso.*, users.user_name
        FROM recurso 
        LEFT JOIN registros 
            ON recurso.recurso_id = registros.idrecurso 
            AND registros.idregistro = (
                SELECT MAX(idregistro) 
                FROM registros AS r
                WHERE r.idrecurso = recurso.recurso_id
            )
        LEFT JOIN users 
            ON registros.idusuario = users.user_id 
        WHERE recurso.recurso_tipo = ?
        ORDER BY recurso.recurso_id
    ");
    
    $stmt->execute([$tipoRecursoId]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($result);
}