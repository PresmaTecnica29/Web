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
$horario_id = $_POST['hora'];
$nombreNet= $_POST['nombreNet'];

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
      $sql = "UPDATE registros SET opcion = 'Accepted', fin_prestamo = ? WHERE idregistro = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("ii", $horario_id, $id);
  } else if ($status == 'denied') {
      $sql = "UPDATE registros SET opcion = 'Denied' WHERE idregistro = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $id);

      $sql3 = "UPDATE registros SET devuelto = 'Accepted' WHERE idregistro = ?";
      $stmt3 = $conn->prepare($sql3);
      $stmt3->bind_param("i", $id);
      $stmt3->execute();

      $sql2 = "UPDATE recurso SET recurso_estado = '1' WHERE  recurso_nombre = ?";  
      $stmt2 = $conn->prepare($sql2);
      $stmt2->bind_param("s", $nombreNet);
      $stmt2->execute(); 
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