<?php
$config = include('../../config/db.php');

// Crear conexión
$conn = new mysqli($config['db']['host'], $config['db']['user'], $config['db']['pass'], $config['db']['name']);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$status = $_POST['status']; // 'accepted' o 'denied'
$nombreNetDevo = $_POST['nombreNetDevo']; // Nombres del recurso (array)

// Verifica si `ids` está en el formato de array
$ids = isset($_POST['ids']) ? $_POST['ids'] : array();
if (!is_array($ids)) {
    $ids = array($ids); // Asegúrate de que `ids` sea siempre un array
}

$successMessages = [];
$errorMessages = [];

foreach ($ids as $id) {
    // Verificar si el registro existe
    $sql = "SELECT * FROM registros WHERE idregistro = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        if ($status == 'accepted') {
            $sqlUpdate = "UPDATE registros SET devuelto = 'Accepted' WHERE idregistro = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("i", $id);
            if ($stmtUpdate->execute() === TRUE) {
                $successMessages[] = "La devolución con id $id ha sido aceptada.";
            } else {
                $errorMessages[] = "Error al actualizar la devolución con id $id: " . $conn->error;
            }
        } else {
            $sqlUpdate = "UPDATE registros SET opcion = 'Denied' WHERE idregistro = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("i", $id);
            if ($stmtUpdate->execute() === TRUE) {
                // Obtener el nombre del recurso correspondiente a este ID
                $recursoNombre = isset($nombreNetDevo[$id]) ? $nombreNetDevo[$id] : '';

                // Actualizar el estado del recurso si el nombre del recurso existe
                
                    $sqlUpdateResource = "UPDATE recurso SET recurso_estado = '1' WHERE recurso_nombre = ?";
                    $stmtUpdateResource = $conn->prepare($sqlUpdateResource);
                    $stmtUpdateResource->bind_param("s", $recursoNombre);
                    $stmtUpdateResource->execute();
                


                
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
