<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Computadoras</title>
<link rel="stylesheet" type="text/css" href="../netbook/style.css">

<style>
    /* Estilo para el modal */
    .modal {
      display: none;
      position: fixed;
      z-index: 1;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content {
      background-color: white;
      margin: 15% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 80%;
      max-width: 500px;
      text-align: center;
    }

    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
    }

    .close:hover,
    .close:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
    }
  </style>

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

  // Acciones de Borrar y Bloquear/Desbloquear Usuario
  if (isset($_POST['accion']) && isset($_POST['user_name'])) {
    $accion = $_POST['accion'];
    $user_name = $_POST['user_name'];
    
    if ($accion === 'activar') {
      // Lógica para borrar al usuario de la base de datos
      $consultaSQL = "UPDATE users SET activado = 1 WHERE user_name = :user_name";
      $sentencia = $conexion->prepare($consultaSQL);
      $sentencia->bindParam(':user_name', $user_name, PDO::PARAM_STR);
      $sentencia->execute();
      
      $mensaje = "Usuario activado exitosamente.";
      
    }
  }

  // Lógica para mostrar la lista de usuarios
  if (isset($_POST['apellido'])) {
    $consultaSQL = "SELECT user_id, user_name, user_email, rol.idRol, rol_descripcion, bloqueado 
                    FROM users 
                    INNER JOIN rol ON users.idRol = rol.idRol 
                    WHERE user_name LIKE '%" . $_POST['apellido'] . "%' 
                    AND activado = 0 ";
  } else {
    $consultaSQL = "SELECT user_id, user_name, user_email, rol.idRol, rol_descripcion, bloqueado 
                    FROM users 
                    INNER JOIN rol ON users.idRol = rol.idRol 
                    WHERE activado = 0 ";
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

if (isset($mensaje)) {
?>
  <div class="container mt-2">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-success" role="alert">
          <?= $mensaje ?>
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
        <a href="abmPersonas.php" class="btn btn-primary mt-4" style="margin-left: 5px;">Volver</a>

      <form method="post" class="form-inline">
        <div class="form-group mr-3" style='margin-top:20px;'>
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
                  <button class="boton" onclick="openDeleteModal('<?= escapar($fila['user_name']) ?>')" style='margin-left:10px;'>🟢 Activar</button>
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
        </tbody>
      </table>
    </div>
  </div>
</div>

  <div id="deleteModal" class="modal">
    <div class="modal-content">
      <p id="deleteModalText"></p>
      <form id="deleteModalForm" method="post">
        <input type="hidden" name="user_name" id="activar_user_name">
        <input type="hidden" name="accion" value="activar">
        <input type="hidden" name="csrf" value="<?php echo escapar($_SESSION['csrf']); ?>">
        <button type="submit" class="btn btn-primary" style="margin-top: 20px; margin-right: 20px; background-color: green; padding-left: 20px; padding-right: 20px;">Sí</button>
        <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()" style="margin-top: 20px; margin-left: 20px; background-color: red; padding-left: 20px; padding-right: 20px;">No</button>
      </form>
    </div>
  </div>

  <div id="blockModal" class="modal">
    <div class="modal-content">
      <p id="blockModalText"></p>
      <form id="blockModalForm" method="post">
        <input type="hidden" name="user_name" id="block_user_name">
        <input type="hidden" name="accion" id="block_action">
        <input type="hidden" name="csrf" value="<?php echo escapar($_SESSION['csrf']); ?>">
        <button type="submit" class="btn btn-primary" style="margin-top: 20px; margin-right: 20px; background-color: green; padding-left: 20px; padding-right: 20px;">Sí</button>
        <button type="button" class="btn btn-secondary" onclick="closeBlockModal()" style="margin-top: 20px; margin-left: 20px; background-color: red; padding-left: 20px; padding-right: 20px;">No</button>
      </form>
    </div>
  </div>

  <script>
  // Funciones para abrir y cerrar el modal de borrar
  function openDeleteModal(user_name) {
    document.getElementById('activar_user_name').value = user_name;
    document.getElementById('deleteModalText').innerText = "¿Estás seguro de que deseas activar al usuario " + user_name + "?";
    document.getElementById('deleteModal').style.display = "block";
  }

  function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = "none";
  }

  // Cerrar el modal cuando se hace clic fuera del contenido del modal
  window.onclick = function(event) {
    var deleteModal = document.getElementById('deleteModal');
    var blockModal = document.getElementById('blockModal');

    // Si el objetivo del clic es el modal (fondo) y no el contenido interno, se cierra el modal
    if (event.target == deleteModal) {
      closeDeleteModal();
    }
    if (event.target == blockModal) {
      closeBlockModal();
    }
  }
</script>

<?php include "../template/footer.php"; ?>
