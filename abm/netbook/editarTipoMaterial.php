<?php
include '../funciones.php';

csrf();
if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  die();
}

$config = include '../../config/db.php';
$conexion = conexion();
$statement = $conexion->prepare("SELECT * FROM area");
$statement->execute();
$areas = $statement->fetchAll();

$resultado = [
  'error' => false,
  'mensaje' => ''
];

// Verifica si se ha recibido un ID válido a través de la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
  $resultado['error'] = true;
  $resultado['mensaje'] = 'El tipo de recurso no existe o el ID es inválido';
} else {
  $tipo_recurso_id = $_GET['id'];

  if (isset($_POST['submit'])) {
    try {
      $conexion = conexion();

      // Actualizar el tipo de recurso con los datos enviados desde el formulario
      $tipo_recurso = [
        "id" => $tipo_recurso_id,
        "nombre" => $_POST['nombre'],
        "area" => $_POST['area']
      ];

      $consultaSQL = "UPDATE tipo_recurso SET
        tipo_recurso_nombre = :nombre,
        tipo_recurso_area = :area
        WHERE tipo_recurso_id = :id";
      $consulta = $conexion->prepare($consultaSQL);
      $consulta->execute($tipo_recurso);

      $resultado['mensaje'] = 'El tipo de recurso ha sido actualizado correctamente';
    } catch (PDOException $error) {
      $resultado['error'] = true;
      $resultado['mensaje'] = $error->getMessage();
    }
  }

  try {
    // Consulta para obtener los datos del tipo de recurso a editar
    $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
    $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);

    $consultaSQL = "SELECT * FROM tipo_recurso WHERE tipo_recurso_id = :id";
    $sentencia = $conexion->prepare($consultaSQL);
    $sentencia->execute(['id' => $tipo_recurso_id]);

    $tipo_recurso = $sentencia->fetch(PDO::FETCH_ASSOC);

    if (!$tipo_recurso) {
      $resultado['error'] = true;
      $resultado['mensaje'] = 'El tipo de recurso no existe';
    }
  } catch (PDOException $error) {
    $resultado['error'] = true;
    $resultado['mensaje'] = $error->getMessage();
  }
}
?>

<?php require "../template/header.php"; ?>

<?php
if ($resultado['error']) {
?>
  <div class="container mt-2">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-danger" role="alert">
          <?= $resultado['mensaje'] ?>
        </div>
      </div>
    </div>
  </div>
<?php
}
?>

<?php
if (isset($_POST['submit']) && !$resultado['error']) {
?>
  <div class="container mt-2">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-success" role="alert">
          <?= $resultado['mensaje'] ?>
        </div>
      </div>
    </div>
  </div>
<?php
}
?>

<?php
if (isset($tipo_recurso) && $tipo_recurso) {
?>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h2 class="mt-4">Editando el Tipo de Material: <?= escapar($tipo_recurso['tipo_recurso_nombre']) ?></h2>
        <hr>
        <form method="post">
          <div class="form-group">
            <label for="nombre">Nombre del Tipo de Recurso</label>
            <input type="text" name="nombre" id="nombre" value="<?= escapar($tipo_recurso['tipo_recurso_nombre']) ?>" class="form-control">
          </div>
          <div class="form-group">
            <label for="area">Área</label>
            <select name="area" id="area" class="form-control">
              <?php
              foreach ($areas as $area) {
                echo '<option value="' . $area['id'] . '" ' . ($tipo_recurso['tipo_recurso_area'] == $area['id'] ? 'selected' : '') . '>' . escapar($area['area_nombre']) . '</option>';
              }
              ?>
            </select>
          </div>
          <div class="form-group" style="margin-top: 20px;">
            <input name="csrf" type="hidden" value="<?php echo escapar($_SESSION['csrf']); ?>">
            <input type="submit" name="submit" class="btn btn-primary" value="Actualizar">
            <a class="btn btn-primary" href="tiposMateriales.php" style="margin-left: 10px;">Regresar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php } ?>

<?php require "../template/footer.php"; ?>