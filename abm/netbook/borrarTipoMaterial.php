<?php
include '../funciones.php';
csrf();
if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  die();
}


$config = include '../../config/db.php';

$resultado = [
  'error' => false,
  'mensaje' => ''
];

if (isset($_POST['confirmar'])) {
  try {
    $conexion = conexion();

    $id = $_POST['id'];
    $consultaSQL = "DELETE FROM tipo_recurso WHERE tipo_recurso_id = :id";

    $sentencia = $conexion->prepare($consultaSQL);
    $sentencia->bindParam(':id', $id);
    $sentencia->execute();

    header('Location: tiposMateriales.php');
    exit();
  } catch (PDOException $error) {
    $resultado['error'] = true;
    $resultado['mensaje'] = $error->getMessage();
  }
}
?>

<?php require "../template/header.php"; ?>

<div class="container mt-2">
  <div class="row">
    <div class="col-md-12">
      <?php if ($resultado['error']) : ?>
        <div class="alert alert-danger" role="alert">
          <?= $resultado['mensaje'] ?>
        </div>
      <?php endif; ?>
      <form method="post">
        <input type="hidden" name="id" value="<?= $_GET['id'] ?>" />
        <p>¿Estás seguro de que quieres eliminar este Tipo de Recurso?</p>
        <button type="submit" name="confirmar" class="btn btn-danger">Sí, eliminar</button>
        <a href="tiposMateriales.php" class="btn btn-secondary">No, regresar</a>
      </form>
    </div>
  </div>
</div>

<?php require "../template/footer.php"; ?>