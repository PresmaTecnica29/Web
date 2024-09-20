<?php

include '../funciones.php';

csrf();
if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  die();
}

if (isset($_POST['submit'])) {
  $resultado = [
    'error' => false,
    'mensaje' => 'El Area  ' . escapar($_POST['area_nombre']) . ' ha sido agregada con éxito'
  ];

  $config = include('../../config/db.php');

  try {
    $conexion = conexion();

    $recurso = [
      "id"   => $_POST['id'],
      "area_nombre"   => $_POST['area_nombre'],
    ];

    $consultaSQL = "INSERT INTO area (id, area_nombre)";
    $consultaSQL .= "values (:" . implode(", :", array_keys($recurso)) . ")";

    $sentencia = $conexion->prepare($consultaSQL);
    $sentencia->execute($recurso);
  } catch (PDOException $error) {
    $resultado['error'] = true;
    $resultado['mensaje'] = $error->getMessage();
  }
}
require_once('../../config/db.php');
$conexion = conexion();
$statement = $conexion->prepare("SELECT * FROM area");
$statement->execute();
$datos = $statement->fetchAll();
?>

<?php include '../template/header.php'; ?>

<?php
if (isset($resultado)) {
?>
  <div class="container mt-3">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-<?= $resultado['error'] ? 'danger' : 'success' ?>" role="alert">
          <?= $resultado['mensaje'] ?>
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
      <h2 class="mt-4" style='margin-bottom:10px;'>Agregar una nueva Area</h2>
      <form method="post">
        <div class="form-group">
          <label for="recurso_id">Codigo del Area</label>
          <input type="text" name="id" id="id" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="recurso_nombre">Nombre del Area</label>
          <input type="text" name="area_nombre" id="area_nombre" class="form-control" required>
        </div>
        <div class="form-group" style='margin-top: 20px'>
        </div>
        <br>
        <div class="form-group">
          <input name="csrf" type="hidden" value="<?php echo escapar($_SESSION['csrf']); ?>">
          <a class="btn btn-primary" href="qr.php" style= 'background-color: red'>Cancelar</a>
          <input type="submit" name="submit" class="btn btn-primary" value="Aceptar" style='margin-left:1px; background-color: green'>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include '../template/footer.php'; ?>