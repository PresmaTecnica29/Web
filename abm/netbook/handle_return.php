<?php
$config = include('../../config/db.php');
// Crear conexión
$conn = new mysqli($config['db']['host'], $config['db']['user'], $config['db']['pass'], $config['db']['name']);

// Verificar conexión
if ($conn->connect_error) {
  die("Conexión fallida: " . $conn->connect_error);
}

$status = $_POST['status'];
$horario_id = $_POST['hora'];
$nombreNet = $_POST['nombreNet'];

// Verifica si `id` está en el formato de array
$ids = isset($_POST['id']) ? $_POST['id'] : array();

if (!is_array($ids)) {
  $ids = array($ids); // Asegúrate de que `ids` sea siempre un array
}

$successMessages = [];
$errorMessages = [];

foreach ($ids as $id) {
    $sql = "SELECT * FROM registros WHERE idregistro = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    $sql2 = "SELECT * FROM recurso WHERE recurso_nombre = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("s", $nombreNet);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    if ($result->num_rows > 0) {
        if ($status == 'accepted') {
            $sqlUpdate = "UPDATE registros SET opcion = 'Accepted', fin_prestamo = ? WHERE idregistro = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("ii", $horario_id, $id);
            if ($stmtUpdate->execute() === TRUE) {
                $successMessages[] = "La devolución con id $id ha sido aceptada.";
            } else {
                $errorMessages[] = "Error al actualizar la devolución con id $id: " . $conn->error;
            }
        } else if ($status == 'denied') {
            $sqlUpdate = "UPDATE registros SET opcion = 'Denied' WHERE idregistro = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("i", $id);
            if ($stmtUpdate->execute() === TRUE) {
                // También se necesita actualizar la tabla de recursos
                $sql3 = "UPDATE registros SET devuelto = 'Accepted' WHERE idregistro = ?";
                $stmt3 = $conn->prepare($sql3);
                $stmt3->bind_param("i", $id);
                $stmt3->execute();

                $sqlUpdateResource = "UPDATE recurso SET recurso_estado = '1' WHERE recurso_nombre = ?";  
                $stmtUpdateResource = $conn->prepare($sqlUpdateResource);
                $stmtUpdateResource->bind_param("s", $nombreNet);
                $stmtUpdateResource->execute();

                $successMessages[] = "La devolución con id $id ha sido rechazada.";
            } else {
                $errorMessages[] = "Error al actualizar la devolución con id $id: " . $conn->error;
            }
        }
    } else {
        $errorMessages[] = "El id $id no es válido.";
    }
}

// Cierra la conexión
$conn->close();

// Envía los mensajes de éxito o error como respuesta
$response = [
    'success' => $successMessages,
    'errors' => $errorMessages
];
echo json_encode($response);
?>