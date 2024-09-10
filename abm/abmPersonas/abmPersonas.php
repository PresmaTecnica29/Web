<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Computadoras</title>
<link rel="stylesheet" type="text/css" href="../netbook/style.css">
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
    $consultaSQL = "SELECT user_id, user_name, user_email, rol.idRol, rol_descripcion, bloqueado 
                    FROM users 
                    INNER JOIN rol ON users.idRol = rol.idRol 
                    WHERE user_name LIKE '%" . $_POST['apellido'] . "%' 
                    LIMIT 100";
} else {
    $consultaSQL = "SELECT user_id, user_name, user_email, rol.idRol, rol_descripcion, bloqueado 
                    FROM users 
                    INNER JOIN rol ON users.idRol = rol.idRol 
                    LIMIT 100";
}
  $sentencia = $conexion->prepare($consultaSQL);
  $sentencia->execute();

  $alumnos = $sentencia->fetchAll();
} catch (PDOException $error) {
  $error = $error->getMessage();
}

$titulo = isset($_POST['apellido']) ? 'Lista de Alumnos (' . $_POST['apellido'] . ')' : 'Lista de Alumnos';
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
    <?php
          if (isset($_SESSION['user_rol'])) {
              if ($_SESSION['user_rol'] == 5 || $_SESSION['user_rol'] == 4) {
                  // Solo se ejecutará este código si el rol del usuario es 5 o 4
                  ?>
                  <a href="agregarUsuario.php" class="btn btn-primary mt-4">Crear Usuario</a>
                  <?php
                    }
                  }
                ?>
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
      <h2 class="mt-3"><?= $titulo ?></h2>
      <table class="table">
        <thead>
          <tr>
            <th>#</th>
            <th>Usuario</th>
            <th>Email</th>
            <th>Rol</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($alumnos && $sentencia->rowCount() > 0) {
            foreach ($alumnos as $fila) {
          ?>
              <tr>
                <td><?php echo escapar($fila["user_id"]); ?></td>
                <td><?php echo escapar($fila["user_name"]); ?></td>
                <td><?php echo escapar($fila["user_email"]); ?></td>
                <td><?php echo escapar($fila["rol_descripcion"]); ?></td>
                <td>
                    <?php
          if (isset($_SESSION['user_rol'])) {
              if ($_SESSION['user_rol'] == 5) {
                  // Solo se ejecutará este código si el rol del usuario es 5 o 4
                  ?>
                  <a href="<?= 'borrarUsuario.php?id=' . escapar($fila["user_id"]) ?>" class="boton">🗑️ Borrar</a>
                  <?php
                    }
                  }
                ?>
                <?php
          if (isset($_SESSION['user_rol'])) {
              if ($_SESSION['user_rol'] == 5 || $_SESSION['user_rol'] == 4) {
                  // Solo se ejecutará este código si el rol del usuario es 5 o 4
                  ?>
                <?php
                if (isset($_SESSION['user_rol'])) {
                  // Si el rol del usuario actual es mayor o igual al rol del usuario listado, se muestra el botón de edición
                  if ($_SESSION['user_rol'] >= $fila["idRol"]) {
                      ?>
                      <a href="<?= 'editarUsuario.php?id=' . escapar($fila["user_id"]) ?>" class="boton">✏️ Editar</a>
                      <?php
                  }
              }
                ?>
                <?php
                    }
                  }
                ?>
                <?php
          if (isset($_SESSION['user_rol'])) {
              if ($_SESSION['user_rol'] == 5 || $_SESSION['user_rol'] == 4) {
                  // Solo se ejecutará este código si el rol del usuario es 5 o 4
                  ?>
                <?php
                if (isset($_SESSION['user_rol'])) {
                  // Si el rol del usuario actual es mayor o igual al rol del usuario listado, se muestra el botón de edición
                  if ($_SESSION['user_rol'] >= $fila["idRol"]) {
                      ?>
                      <?php if ($fila["bloqueado"] == 0): ?>
                      <a href="<?= 'bloquearUsuario.php?id=' . escapar($fila["user_id"]) ?>" class="boton">❌ Bloquear</a>
                    <?php endif; ?>
                  <?php if ($fila["bloqueado"] == 1): ?>
                    <a href="<?= 'desbloquearUsuario.php?id=' . escapar($fila["user_id"]) ?>" class="boton">✅ Desbloquear</a>
                  <?php endif; ?>
                      <?php
                  }
              }
                ?>
                <?php
                    }
                  }
                ?>
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