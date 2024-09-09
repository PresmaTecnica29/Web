<?php
include '../funciones.php';

csrf();
if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  die();
}

$config = include '../../config/db.php';
$conexion = conexion();
$statement = $conexion->prepare("SELECT * FROM rol");
$statement->execute();
$datos = $statement->fetchAll();

$resultado = [
  'error' => false,
  'mensaje' => ''
];

if (!isset($_GET['id'])) {
  $resultado['error'] = true;
  $resultado['mensaje'] = 'El alumno no existe';
}

if (isset($_POST['submit'])) {
  try {
    $conexion = conexion();

    $alumno = [
      "id"        => $_GET['id'],
      "nombre"    => $_POST['nombre'],
      "email"     => $_POST['email'],
      "rol"      => $_POST['rol'],
    ];

    $consultaSQL = "UPDATE users SET
        user_name = :nombre,
        user_email = :email,
        idRol = :rol
        WHERE user_id = :id";
    $consulta = $conexion->prepare($consultaSQL);
    $consulta->execute($alumno);
  } catch (PDOException $error) {
    $resultado['error'] = true;
    $resultado['mensaje'] = $error->getMessage();
  }
}

try {
  $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
  $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);

  $id = $_GET['id'];
  $consultaSQL = "SELECT * FROM users WHERE user_id =" . $id;

  $sentencia = $conexion->prepare($consultaSQL);
  $sentencia->execute();

  $alumno = $sentencia->fetch(PDO::FETCH_ASSOC);

  if (!$alumno) {
    $resultado['error'] = true;
    $resultado['mensaje'] = 'No se ha encontrado el alumno';
  }
} catch (PDOException $error) {
  $resultado['error'] = true;
  $resultado['mensaje'] = $error->getMessage();
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
          El usuario ha sido actualizado correctamente
        </div>
      </div>
    </div>
  </div>
<?php
}
?>

<?php
if (isset($alumno) && $alumno) {
?>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h2 class="mt-4">Editando el usuario <?= escapar($alumno['user_name']) . ' ' ?></h2>
        <hr>
        <form method="post">
          <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="<?= escapar($alumno['user_name']) ?>" class="form-control">
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?= escapar($alumno['user_email']) ?>" class="form-control">
          </div>
          <br>
          <div class="form-group" style='margin-bottom:10px;'>
            <label for="rol">Rol</label>
            <?php
              isset($_SESSION['user_rol'])
                      // Solo se ejecutará este código si el rol del usuario es 5
                      ?>
                      <select name="rol_seleccionado" class="input">
                          <?php 
                          // Creamos un array para almacenar los roles de 4 para abajo
                          $rolesPermitidos = [];

                          // Recorremos los datos y filtramos los roles que sean menores a 5
                          foreach ($datos as $dato) { 
                              if ($dato['idRol'] < $_SESSION['user_rol']) {  // Solo mostramos los roles con id menores a 5
                                  echo '<option value="' . $dato['idRol'] . '" class="input">' . $dato['rol_descripcion'] . '</option>';
                              }
                          }
                          ?>
                      </select>
                      <?php
                    }
                
              ?>
          </div>
          <div class="form-group">
            <input name="csrf" type="hidden" value="<?php echo escapar($_SESSION['csrf']); ?>">
            <input type="submit" name="submit" class="btn btn-primary" value="Actualizar">
            <a class="btn btn-primary" href="abmPersonas.php" style="margin-left: 10px;">Regresar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php

?>

<?php require "../template/footer.php"; ?>