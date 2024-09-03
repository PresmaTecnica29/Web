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

  $consultaSQL = "SELECT registros.idregistro, users.user_name, recurso.recurso_nombre, DATE_FORMAT(registros.inicio_prestamo, '%d/%m %H:%i') AS inicio_prestamo, registros.opcion FROM registros inner join recurso on recurso.recurso_id = registros.idrecurso inner join users on registros.idusuario = users.user_id  where registros.opcion = 'Pending' ";
  $sentencia = $conexion->prepare($consultaSQL);
  $sentencia->execute();

  $notification = null;
  if ($sentencia->rowCount() > 0) {
    // Si hay una devolución pendiente, se almacenará en $notification
    $notifications = $sentencia->fetchAll(PDO::FETCH_ASSOC);
  }

  $consultaSQL = "SELECT registros.idregistro, users.user_name, recurso.recurso_nombre, DATE_FORMAT(registros.inicio_prestamo, '%d/%m %H:%i') AS inicio_prestamo, DATE_FORMAT(horario.horario, '%H:%i') AS horario, registros.devuelto FROM registros inner join recurso on recurso.recurso_id = registros.idrecurso inner join users on registros.idusuario = users.user_id inner join horario on horario.id = registros.fin_prestamo  where registros.devuelto = 'Pending'";
  $sentencia = $conexion->prepare($consultaSQL);
  $sentencia->execute();

  $notificationDevolucion = null;
  if ($sentencia->rowCount() > 0) {
    // Si hay una devolución pendiente, se almacenará en $notification
    $notificationDevolucion = $sentencia->fetchAll(PDO::FETCH_ASSOC);
  }

  if (isset($_POST['apellido'])) {
    $consultaSQL = "SELECT registros.idregistro, users.user_name, DATE_FORMAT(registros.inicio_prestamo, '%d/%m %H:%i') AS inicio_prestamo, DATE_FORMAT(horario.horario, '%H:%i') AS fin_prestamo, COALESCE(registros.fechas_extendidas, '----') AS fechas_extendidas, recurso.recurso_nombre FROM registros INNER JOIN users ON registros.idusuario = users.user_id INNER JOIN recurso ON recurso.recurso_id = registros.idrecurso INNER JOIN horario ON horario.id = registros.fin_prestamo WHERE registros.opcion <> 'Pending' AND registros.devuelto <> 'Accepted' ORDER BY registros.idregistro desc;";
  } else {
    $consultaSQL = "SELECT registros.idregistro, users.user_name, DATE_FORMAT(registros.inicio_prestamo, '%d/%m %H:%i') AS inicio_prestamo, DATE_FORMAT(horario.horario, '%H:%i') AS fin_prestamo, COALESCE(registros.fechas_extendidas, '----') AS fechas_extendidas, recurso.recurso_nombre FROM registros INNER JOIN users ON registros.idusuario = users.user_id INNER JOIN recurso ON recurso.recurso_id = registros.idrecurso INNER JOIN horario ON horario.id = registros.fin_prestamo WHERE registros.opcion <> 'Pending' AND registros.devuelto <> 'Accepted' ORDER BY registros.idregistro desc ;";
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
$titulo = isset($_POST['apellido']) ? 'Lista de prestamos (' . $_POST['apellido'] . ')' : 'Prestamos Activos';
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
      
      <a href="devuelto.php" class="btn btn-primary mt-4">Ver Devueltos</a>
      <hr>

      <form method="post" class="form-inline">
        <div class="form-group mr-3">
          <input type="text" id="apellido" name="apellido" placeholder="Buscar por Usuario" class="form-control">
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
            <th>Alumno</th>
            <th>Inicio prestamo</th>
            <th>Fin prestamo</th>
            <th>Fechas extendidas</th>
            <th>Material retirado</th>
          </tr>
        </thead>
        <tbody id="cuerpoDeTabla">
          <?php
          if ($alumnos && $sentencia->rowCount() > 0) {
            foreach ($alumnos as $fila) {
          ?>
              <tr>
                <td><?php echo escapar($fila["idregistro"]); ?></td>
                <td><?php echo escapar($fila["user_name"]); ?></td>
                <td><?php echo escapar(($fila["inicio_prestamo"])); ?></td>
                <td><?php echo escapar($fila["fin_prestamo"]); ?></td>
                <td><?php echo escapar($fila["fechas_extendidas"]); ?></td>
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
<!-- <div class="modal" tabindex="-1" role="dialog" id="returnNotificationModal"> -->
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Notificación de Peticion</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">


      <?php
if (!empty($notifications)) {
    echo '<table border="1">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Seleccionar</th>';  // Añadido para el checkbox
    echo '<th>Alumno</th>';
    echo '<th>Material</th>';
    echo '<th>Horario inicio</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    // Recorre cada notificación en el array $notifications
    foreach ($notifications as $notification) {
        echo '<tr>';
        echo '<td><input type="checkbox" name="notifications[]" value="' . $notification['idregistro'] . '"></td>'; // Añadido el checkbox
        echo '<td>' . $notification['user_name'] . '</td>';
        echo '<td>' . $notification['recurso_nombre'] . '</td>';
        echo '<td>' . $notification['inicio_prestamo'] . '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '<div class="modal-footer">';
    echo '<button type="button" class="btn btn-success" id="acceptDevolucion">Aceptar</button>';
    echo '<button type="button" class="btn btn-danger" id="denyDevolucion">Rechazar</button>';
    echo '</div>';
} else {
    echo 'No hay notificaciones pendientes.';
}
?>


<br>

        <p id="notificationMessageUser">Alumno: <?php echo isset($notification['user_name']) ? $notification['user_name'] : ''; ?></p>
        <p id="notificationMessageResource">Material: <?php echo isset($notification['recurso_nombre']) ? $notification['recurso_nombre'] : ''; ?></p>
        <p id="notificationMessageStart">Horario inicio: <?php echo isset($notification['inicio_prestamo']) ? $notification['inicio_prestamo'] : ''; ?></p>
        <div class="form-group">
          <label for="horario">Horario</label>
          <select name="horario" id="horario" class="input">
            <option value='' disabled hidden selected>Elegir un horario</option>
            <?php foreach ($datos as $dato) : ?>
              <option value="<?= $dato['id']; ?>" class="input"><?= $dato['horario'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <label for="nombreNet"></label>
            <select name="nombreNet" id="nombreNet" class="input">
              <option  value='<?= ($notification['recurso_nombre'])?>' class="input"> <?= ($notification['recurso_nombre'])?></option>
            </select>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="acceptReturn">Aceptar</button>
        <button type="button" class="btn btn-danger" id="denyReturn">Rechazar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="returnDevolucionModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Notificación de Devolucion</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <p id="devolucionMessage"></p>
        <p id="devolucionMessageUser">Alumno: <?php echo isset($notificationDevolucion['user_name']) ? $notificationDevolucion['user_name'] : ''; ?></p>
        <p id="devolucionMessageResource">Material: <?php echo isset($notificationDevolucion['recurso_nombre']) ? $notificationDevolucion['recurso_nombre'] : ''; ?></p>
        <p id="devolucionMessageStart">Horario inicio: <?php echo isset($notificationDevolucion['inicio_prestamo']) ? $notificationDevolucion['inicio_prestamo'] : ''; ?></p>
        <p id="devolucionMessageEnd">Horario final: <?php echo isset($notificationDevolucion['horario']) ? $notificationDevolucion['horario'] : ''; ?></p>
        <label for="nombreNetDevo"></label>
            <select name="nombreNetDevo" id="nombreNetDevo" class="input" style='display:none'>
              <option  value='<?= ($notificationDevolucion['recurso_nombre'])?>' class="input"> <?= ($notificationDevolucion['recurso_nombre'])?></option>
            </select>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="acceptDevolucion">Aceptar</button>
        <button type="button" class="btn btn-danger" id="denyDevolucion">Rechazar</button>
      </div>
    </div>
  </div>
</div>





<?php include "../template/footer.php"; ?>