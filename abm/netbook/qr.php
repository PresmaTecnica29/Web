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
    $consultaSQL = "SELECT recurso.recurso_id, recurso.recurso_nombre, estado.descripcion_estado, area.area_nombre FROM recurso INNER JOIN area ON recurso.recurso_tipo = area.id inner join estado on recurso.recurso_estado = estado.idEstado AND recurso.recurso_id LIKE '%" . $_POST['apellido'] . "%' limit 100;";
  } else {
    $consultaSQL = "SELECT recurso.recurso_id, recurso.recurso_nombre, estado.descripcion_estado, area.area_nombre FROM recurso INNER JOIN area ON recurso.recurso_tipo = area.id inner join estado on recurso.recurso_estado = estado.idEstado;";
  }

  $sentencia = $conexion->prepare($consultaSQL);
  $sentencia->execute();

  $alumnos = $sentencia->fetchAll();
} catch (PDOException $error) {
  $error = $error->getMessage();
}

$titulo = isset($_POST['apellido']) ? 'Lista de Materiales (' . $_POST['apellido'] . ')' : 'Lista de Materiales';
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
      <a href="agregarMaterial.php" class="btn btn-primary mt-4">Agregar material</a>
      <hr>

      <form method="post" class="form-inline">
        <div class="form-group mr-3">
          <input type="text" id="apellido" name="apellido" placeholder="Buscar por Id" class="form-control">
        </div>
        <input name="csrf" type="hidden" value="<?php echo escapar($_SESSION['csrf']); ?>"><br>
      </form>
    </div>
  </div>
</div>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <h2 class="mt-3"><?= $titulo ?></h2>
      <table class="table">
        <thead>
          <tr>
            <th>#</th>
            <th>Material</th>
            <th>Estado</th>
            <th>Area</th>
          </t
        </thead>
        <tbody>
          <?php
          if ($alumnos && $sentencia->rowCount() > 0) {
            foreach ($alumnos as $fila) {
          ?>
              <tr>
                <td><?php echo escapar($fila["recurso_id"]); ?></td>
                <td><?php echo escapar($fila["recurso_nombre"]); ?></td>
                <td><?php echo escapar($fila["descripcion_estado"]); ?></td>
                <td><?php echo escapar($fila["area_nombre"]); ?></td>
                <td>
                  <a href="<?= 'generar_qr.php?id=' . escapar($fila["recurso_id"] . '&nombre=' . escapar(($fila["recurso_nombre"]))) ?>">Generar Qr</a>
                  <a href="<?= 'abrirqr.php?nombre=' . escapar($fila["recurso_nombre"]) ?>">Abrir Qr</a>
                </td>
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