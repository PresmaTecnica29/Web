<?php
$config = include('../../config/db.php');
// Crear conexión
$conn = new mysqli($config['db']['host'], $config['db']['user'], $config['db']['pass'], $config['db']['name']);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$status = $_POST['status'];
$horarios = $_POST['horarios']; // Ahora recibe un array de horarios
$fechasDevolucion = $_POST['fechasDevolucion']; // Ahora recibe un array de fechas de devolución
$nombresNet = $_POST['nombresNet']; // Ahora recibe un array de nombresNet

// Verifica si `id` está en el formato de array
$ids = isset($_POST['id']) ? $_POST['id'] : array();

if (!is_array($ids)) {
    $ids = array($ids); // Asegúrate de que `ids` sea siempre un array
}

$successMessages = [];
$errorMessages = [];

foreach ($ids as $id) {
    // Verificar si el ID de registro es válido
    $sql = "SELECT * FROM registros WHERE idregistro = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si el nombre del recurso existe
    $nombreNet = $nombresNet[$id]; // Obtener el nombre del recurso específico para este ID
    $sql2 = "SELECT * FROM recurso WHERE recurso_nombre = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("s", $nombreNet);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    if ($result->num_rows > 0) {
        if ($status == 'accepted') {
            $fin_prestamo_fecha = $fechasDevolucion[$id]; // Obtener la fecha de devolución correspondiente a este ID
            $sqlUpdate = "UPDATE registros SET opcion = 'Accepted', fin_prestamo = ?, fin_prestamo_fecha = ? WHERE idregistro = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("isi", $horarios[$id], $fin_prestamo_fecha, $id); // Actualiza también la fecha
            if ($stmtUpdate->execute() === TRUE) {
                $successMessages[] = "El préstamo con la ID $id ha sido aceptado.";
            } else {
                $errorMessages[] = "Error al actualizar el préstamo con la ID $id: " . $conn->error;
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

                $successMessages[] = "El préstamo con la ID $id ha sido rechazado.";
            } else {
                $errorMessages[] = "Error al actualizar el préstamo con la ID $id: " . $conn->error;
            }
        }
    } else {
        $errorMessages[] = "El ID $id no es válido.";
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
  