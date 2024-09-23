<?php

include '../funciones.php';

// Implementación alternativa de hash_equals si no está disponible
if (!function_exists('hash_equals')) {
  function hash_equals($str1, $str2) {
    if (strlen($str1) != strlen($str2)) {
      return false;
    } else {
      $res = $str1 ^ $str2;
      $ret = 0;
      for ($i = strlen($res) - 1; $i >= 0; $i--) {
        $ret |= ord($res[$i]);
      }
      return !$ret;
    }
  }
}

// Generación del token CSRF
csrf();

// Validación del token CSRF y procesamiento del formulario
if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  die();
}

if (isset($_POST['submit'])) {
  $resultado = [
    'error' => false,
    'mensaje' => ' ' . escapar($_POST['tipo_recurso_nombre']) . ' ha sido agregado con éxito'
  ];

  $config = include('../../config/db.php');

  try {
    $conexion = conexion();

    $recurso = [
      "tipo_recurso_id"   => $_POST['tipo_recurso_id'],
      "tipo_recurso_nombre"   => $_POST['tipo_recurso_nombre'],
      "tipo_recurso_area"   => $_POST['tipo_recurso_area'],
    ];

    $consultaSQL = "INSERT INTO tipo_recurso (tipo_recurso_id, tipo_recurso_nombre, tipo_recurso_area)";
    $consultaSQL .= " VALUES (:" . implode(", :", array_keys($recurso)) . ")";

    $sentencia = $conexion->prepare($consultaSQL);
    $sentencia->execute($recurso);
  } catch (PDOException $error) {
    $resultado['error'] = true;
    $resultado['mensaje'] = $error->getMessage();
  }
}

// Consultar áreas y tipos de recurso para mostrar en los select
require_once('../../config/db.php');
$conexion = conexion();
$statement = $conexion->prepare("SELECT * FROM tipo_recurso");
$statement->execute();
$datos = $statement->fetchAll();
$statement2 = $conexion->prepare("SELECT * FROM area");
$statement2->execute();
$datosArea = $statement2->fetchAll();

?>

<?php include '../template/header.php'; ?>

<!-- Mostrar el resultado del proceso de inserción (éxito o error) -->
<?php if (isset($resultado)) : ?>
  <div class="container mt-3">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-<?= $resultado['error'] ? 'danger' : 'success' ?>" role="alert">
          <?= $resultado['mensaje'] ?>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <h2 class="mt-4" style='margin-bottom:10px;'>Agregar tipo de material</h2>
      <form method="post">
        <div class="form-group">
          <label for="recurso_id">Codigo del Tipo de Recurso</label>
          <input type="text" name="tipo_recurso_id" id="tipo_recurso_id" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="recurso_nombre">Nombre del Tipo de Recurso</label>
          <input type="text" name="tipo_recurso_nombre" id="tipo_recurso_nombre" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="tipo_recurso_area" style='margin-top: 20px; margin-bottom: 20px'>Area</label>
          <select name="tipo_recurso_area" id="tipo_recurso_area" class="input" required>
            <option value="" disabled hidden selected >Elegir un Area</option>
            <?php foreach ($datosArea as $dato) : ?>
              <option value="<?= $dato['id'] ?>" class="input"><?= $dato['area_nombre'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <br>
        <div class="form-group">
          <input name="csrf" type="hidden" value="<?php echo escapar($_SESSION['csrf']); ?>">
          <a class="btn btn-primary" href="tiposMateriales.php" style= 'background-color: red'>Cancelar</a>
          <input type="submit" name="submit" class="btn btn-primary" value="Aceptar" style='margin-left:1px; background-color: green'>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include '../template/footer.php'; ?>