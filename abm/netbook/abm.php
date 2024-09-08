<style>
  /* Estilos para la tabla */
  table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
  }

  th,
  td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
  }

  th {
    background-color: #e0e0e0;
    ;
  }

  /* Estilos para las filas */
  tr:hover {
    background-color: #f5f5f5;
  }

  /* Estilos para el select */
  select {
    width: 100%;
    padding: 8px;
    margin: 4px 0;
    border: 1px solid #ccc;
    border-radius: 4px;
  }

  /* Estilos para el contenedor del select */
  .form-group {
    margin: 0;
  }

  /* Estilos para los botones */
  .modal-footer {
    display: flex;
    justify-content: flex-end;
    padding: 10px;
  }

  .btn {
    padding: 10px 20px;
    margin-left: 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
  }

  .btn-success {
    background-color: #28a745;
    color: white;
  }

  .btn-success:hover {
    background-color: #218838;
  }

  .btn-danger {
    background-color: #dc3545;
    color: white;
  }

  .btn-danger:hover {
    background-color: #c82333;
  }
</style>

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
<div class="modal" tabindex="-1" role="dialog" id="returnNotificationModal">
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
          echo '<th>#</th>';  // Añadido para el checkbox
          echo '<th>Alumno</th>';
          echo '<th>Material</th>';
          echo '<th>Horario inicio</th>';
          echo '<th>Horario de devolución</th>';
          echo '<th></th>';
          echo '</tr>';
          echo '</thead>';
          echo '<tbody>';

          foreach ($notifications as $notification) {
            echo '<tr>';
            // Checkbox para cada notificación
            echo '<td><input type="checkbox" name="notifications[]" value="' . $notification['idregistro'] . '" class="checkboxNotification"></td>';
            echo '<td><p id="notificationMessageUser">' . $notification['user_name'] . '</p></td>';
            echo '<td><p id="notificationMessageResource">' . $notification['recurso_nombre'] . '</p></td>';
            echo '<td><p id="notificationMessageStart">' . $notification['inicio_prestamo'] . '</p></td>';

            echo '<td>';
            echo '<div class="form-group">';
            echo '<select name="horario[' . $notification['idregistro'] . ']" id="horario" class="input">';
            echo '<option value="" disabled hidden selected>Elegir un horario</option>';

            foreach ($datos as $dato) {
              echo '<option value="' . $dato['id'] . '">' . $dato['horario'] . '</option>';
            }

            echo '</select>';
            echo '</div>';
            echo '</td>';

            echo '<td>';
            echo '<label for="nombreNetDevo"></label>';
            echo '<select name="nombreNet" id="nombreNet" class="input">';
            echo '<option value="' . $notification['recurso_nombre'] . '">' . $notification['recurso_nombre'] . '</option>';
            echo '</select>';
            echo '</td>';

            echo '</tr>';
          }

          echo '</tbody>';
          echo '</table>';

          // Footer con los botones
          echo '<div class="modal-footer">';

          // Botones de aceptar y rechazar
          echo '<div class="d-grid gap-2 d-md-flex justify-content-md-end">';
          echo '<button type="button" class="btn btn-success" id="acceptReturn" disabled>Aceptar</button>';
          echo '<button type="button" class="btn btn-danger" id="denyReturn" disabled>Rechazar</button>';
          echo '</div>';
          // Botones de marcar y desmarcar
          echo '<div >';
          echo '<button type="button" class="btn btn-secondary btn-sm btn btn-outline-ligth"   onclick="toggleCheckboxes(true)">Marcar todas</button>';
          echo '<button type="button" class="btn btn-secondary btn-sm btn btn-outline-ligth"   onclick="toggleCheckboxes(false)">Desmarcar todas</button>';
          echo '</div>';
          echo '</div>';
        } else {
          echo 'No hay notificaciones pendientes.';
        }
        ?>

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
        <?php
        if (!empty($notificationDevolucion)) {
          echo '<table border="1">';
          echo '<thead>';
          echo '<tr>';
          echo '<th>#</th>';  // Añadido para el checkbox
          echo '<th>Alumno</th>';
          echo '<th>Material</th>';
          echo '<th>Horario inicio</th>';
          echo '<th>Horario final</th>';
          echo '<th></th>';
          echo '</tr>';
          echo '</thead>';
          echo '<tbody>';

          foreach ($notificationDevolucion as $devolucion) {
            echo '<tr>';
            // Checkbox para seleccionar devoluciones
            echo '<td><input type="checkbox" name="notificationDevolucion[]" value="' . $devolucion['idregistro'] . '" class="checkboxDevolucion"></td>';
            echo '<td><p id="devolucionMessageUser">' . (isset($devolucion['user_name']) ? $devolucion['user_name'] : '') . '</p></td>';
            echo '<td><p id="devolucionMessageResource">' . (isset($devolucion['recurso_nombre']) ? $devolucion['recurso_nombre'] : '') . '</p></td>';
            echo '<td><p id="devolucionMessageStart">' . (isset($devolucion['inicio_prestamo']) ? $devolucion['inicio_prestamo'] : '') . '</p></td>';
            echo '<td><p id="devolucionMessageEnd">' . (isset($devolucion['horario']) ? $devolucion['horario'] : '') . '</p></td>';

            // Selector oculto con el nombre del recurso
            echo '<td>';
            echo '<label for="nombreNetDevo"></label>';
            echo '<select name="nombreNetDevo[' . $devolucion['idregistro'] . ']" id="nombreNetDevo" class="input" style="display:none">';
            echo '<option value="' . (isset($devolucion['recurso_nombre']) ? $devolucion['recurso_nombre'] : '') . '" class="input">';
            echo (isset($devolucion['recurso_nombre']) ? $devolucion['recurso_nombre'] : '');
            echo '</option>';
            echo '</select>';
            echo '</td>';
            echo '</tr>';
          }

          echo '</tbody>';
          echo '</table>';

          // Botones de aceptar, rechazar, marcar y desmarcar todas
          echo '<div class="modal-footer">';
          echo '<div class="d-grid gap-2 d-md-flex justify-content-md-end">';
          echo '<button type="button" class="btn btn-success" id="acceptDevolucion" disabled>Aceptar</button>';
          echo '<button type="button" class="btn btn-danger" id="denyDevolucion" disabled>Rechazar</button>';
          echo '</div>';

          echo '<div>';
          echo '<button type="button" class="btn btn-secondary btn-sm btn btn-outline-ligth" onclick="toggleCheckboxesDevolucion(true)">Marcar todas</button>';
          echo '<button type="button" class="btn btn-secondary btn-sm btn btn-outline-ligth" onclick="toggleCheckboxesDevolucion(false)">Desmarcar todas</button>';
          echo '</div>';
          echo '</div>';
        } else {
          echo '<p>No hay devoluciones pendientes.</p>';
        }
        ?>




      </div>
    </div>
  </div>



</div>
</div>
</div>
</div>





<?php include "../template/footer.php"; ?>