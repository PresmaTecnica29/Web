<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Computadoras</title>
<link rel="stylesheet" type="text/css" href="style.css">
<style>

</style>

<script>
  // Obtener el horario actual
var currentTime = new Date().getHours();

// Obtener el horario de fin del préstamo de una computadora (ejemplo)
var finPrestamo = 18; // Horario de fin del préstamo (en este caso, 18:00)

// Comparar los horarios y mostrar u ocultar la computadora en la pantalla
if (currentTime > finPrestamo) {
    // Mostrar la computadora en la pantalla
    document.getElementById("computadora").style.display = "block";
} else {
    // Ocultar la computadora en la pantalla
    document.getElementById("computadora").style.display = "none";
}
</script>

</head>

<?php
include '../funciones.php';

csrf();
if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  die();
}

$error = false;
$config = include('../../config/db.php');


try {
  $conexion = conexion();

  if (isset($_POST['apellido'])) {
    $consultaSQL = "SELECT registros.idregistro, users.user_name, DATE_FORMAT(registros.inicio_prestamo, '%d/%m %H:%i') AS inicio_prestamo, DATE_FORMAT(horario.horario, '%H:%i') AS fin_prestamo, COALESCE(registros.fechas_extendidas, '----') AS fechas_extendidas, recurso.recurso_nombre FROM registros INNER JOIN users ON registros.idusuario = users.user_id INNER JOIN recurso ON recurso.recurso_id = registros.idrecurso INNER JOIN horario ON horario.id = registros.fin_prestamo WHERE registros.opcion = 'Accepted' AND registros.devuelto = 'Pending' OR registros.devuelto = 'Denied' AND users.user_name LIKE '%" . $_POST['apellido'] . "%' ORDER BY registros.idregistro DESC";
  } else {
    $consultaSQL = "SELECT registros.idregistro, users.user_name, DATE_FORMAT(registros.inicio_prestamo, '%d/%m %H:%i') AS inicio_prestamo, DATE_FORMAT(horario.horario, '%H:%i') AS fin_prestamo, COALESCE(registros.fechas_extendidas, '----') AS fechas_extendidas, recurso.recurso_nombre FROM registros INNER JOIN users ON registros.idusuario = users.user_id INNER JOIN recurso ON recurso.recurso_id = registros.idrecurso INNER JOIN horario ON horario.id = registros.fin_prestamo WHERE registros.opcion = 'Accepted' AND registros.devuelto = 'Pending' OR registros.devuelto = 'Denied' ORDER BY registros.idregistro desc ";
  }

  $sentencia = $conexion->prepare($consultaSQL);
  $sentencia->execute();

  $alumnos = $sentencia->fetchAll();
} catch (PDOException $error) {
  $error = $error->getMessage();
}
$conexion = conexion();
$statement = $conexion->prepare("SELECT id, DATE_FORMAT(horario, '%H:%i') AS horario FROM horario");
$statement->execute();
$datos = $statement->fetchAll();
$titulo = isset($_POST['apellido']) ? 'Lista de prestamos (' . $_POST['apellido'] . ')' : 'Prestamos Expirados';

date_default_timezone_set('America/Argentina/Buenos_Aires'); // Ajusta la zona horaria según tu ubicación
$currentTime = date('H:i:s');

if (isset($_POST['apellido'])) {
    $consultaSQL = "SELECT registros.idregistro, users.user_name, DATE_FORMAT(registros.inicio_prestamo, '%d/%m %H:%i') AS inicio_prestamo, 
               DATE_FORMAT(horario.horario, '%H:%i') AS fin_prestamo, COALESCE(registros.fechas_extendidas, '----') AS fechas_extendidas, 
               recurso.recurso_nombre 
        FROM registros 
        INNER JOIN users ON registros.idusuario = users.user_id 
        INNER JOIN recurso ON recurso.recurso_id = registros.idrecurso 
        INNER JOIN horario ON horario.id = registros.fin_prestamo 
        WHERE registros.opcion = 'Accepted' AND registros.devuelto = 'Pending' OR registros.devuelto = 'Denied' 
              AND users.user_name LIKE :apellido 
              AND horario.horario < :currentTime
        ORDER BY registros.idregistro DESC";
} else {
    $consultaSQL = "SELECT registros.idregistro, users.user_name, DATE_FORMAT(registros.inicio_prestamo, '%d/%m %H:%i') AS inicio_prestamo, 
               DATE_FORMAT(horario.horario, '%H:%i') AS fin_prestamo, COALESCE(registros.fechas_extendidas, '----') AS fechas_extendidas, 
               recurso.recurso_nombre 
        FROM registros 
        INNER JOIN users ON registros.idusuario = users.user_id 
        INNER JOIN recurso ON recurso.recurso_id = registros.idrecurso 
        INNER JOIN horario ON horario.id = registros.fin_prestamo 
        WHERE registros.opcion = 'Accepted' AND registros.devuelto = 'Pending' OR registros.devuelto = 'Denied' 
              AND horario.horario < :currentTime
        ORDER BY registros.idregistro DESC";
}

$sentencia = $conexion->prepare($consultaSQL);

if (isset($_POST['apellido'])) {
    $sentencia->bindValue(':apellido', '%' . $_POST['apellido'] . '%', PDO::PARAM_STR);
}

$sentencia->bindValue(':currentTime', $currentTime, PDO::PARAM_STR);
$sentencia->execute();

$alumnos = $sentencia->fetchAll();
?>


<?php include "../template/header.php"; ?>

<?php
if ($error) {
?>
  <div class="container mt-2">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-danger" role="alert">
          <?= $error ?>
        </div>
      </div>
    </div>
  </div>
<?php
}
?>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <hr>

      <form method="post" class="form-inline">
        <div class="form-group mr-3">
          <input type="text" id="apellido" name="apellido" placeholder="Buscar por Apellido" class="form-control">
        </div>
        <input name="csrf" type="hidden" value="<?php echo escapar($_SESSION['csrf']); ?>"><br>
      </form>
    </div>
  </div>
</div>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <h2 class="mt-3">
        <?= $titulo ?>
      </h2>
      <table class="table">
        <thead>
          <tr>
            <th>#</th>
            <th>Usuario</th>
            <th>Fin prestamo</th>
            <th>Material retirado</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($alumnos && $sentencia->rowCount() > 0) {
            foreach ($alumnos as $fila) {
          ?>
              <tr>
                <td><?php echo escapar($fila["idregistro"]); ?></td>
                <td><?php echo escapar($fila["user_name"]); ?></td>
                <td><?php echo escapar($fila["fin_prestamo"]); ?></td>
                <td><?php echo escapar($fila["recurso_nombre"]); ?></td>
              </tr>
          <?php
            }
          }
          ?>
        <tbody>
      </table>
    </div>
  </div>
</div>







<?php include "../template/footer.php"; ?>