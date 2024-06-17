<?php
$config = include('../../config/db.php');

// Crear conexión
$conn = new mysqli($config['db']['host'], $config['db']['user'], $config['db']['pass'], $config['db']['name']);

// Verificar conexión
if ($conn->connect_error) {
  die("Conexión fallida: " . $conn->connect_error);
}
$status = $_POST['status']; 
$id = $_POST['id'];

$sql = "SELECT * FROM registros WHERE idregistro = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  if ($status == 'accepted') {
      $sql = "UPDATE registros SET devuelto = 'Accepted' WHERE idregistro = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $id);
  } else if ($status == 'denied') {
      $sql = "UPDATE registros SET devuelto = 'Denied' WHERE idregistro = ?"; 
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $id);
  }

  if ($stmt->execute() === TRUE) {
      echo "La devolución ha sido " . ($status == 'accepted' ? 'aceptada' : 'rechazada') . ".";
  } else {
      echo "Error al actualizar el estado de la devolución: " . $conn->error;
  }
} else {
  echo "El id del recurso proporcionado no es válido.";
}

$conn->close();
?>
