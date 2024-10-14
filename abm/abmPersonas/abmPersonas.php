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

    
    input[type="checkbox"] {
      width: 20px;
      height: 20px;
      margin-right: 10px;
    }

    /* Estilo para el menú desplegable */
    .dropdown {
      position: relative;
      display: inline-block;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      background-color: #f9f9f9;
      min-width: 70px;
      box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
      padding: 12px 16px;
      z-index: 1;
    }

    .dropdown-content input[type="checkbox"] {
      display: block;
      margin-bottom: 10px;
    }

    .dropdown:hover .dropdown-content {
      display: block;
    }

    .dropdown:hover .dropbtn {
      background-color: #3e8e41;
    }

    .dropbtn {
      background-color: #4CAF50;
      color: white;
      padding: 10px;
      font-size: 16px;
      border: none;
      cursor: pointer;
    }

    .checkbox-wrapper {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }

    .checkbox-wrapper input[type="checkbox"] {
      margin-right: 10px;
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
    
    if ($accion === 'borrar') {
      // Lógica para borrar al usuario de la base de datos
      $consultaSQL = "DELETE FROM users WHERE user_name = :user_name";
      $sentencia = $conexion->prepare($consultaSQL);
      $sentencia->bindParam(':user_name', $user_name, PDO::PARAM_STR);
      $sentencia->execute();
      
      $mensaje = "Usuario borrado exitosamente.";
      
    } elseif ($accion === 'bloquear') {
      // Lógica para bloquear al usuario
      $consultaSQL = "UPDATE users SET bloqueado = 1 WHERE user_name = :user_name";
      $sentencia = $conexion->prepare($consultaSQL);
      $sentencia->bindParam(':user_name', $user_name, PDO::PARAM_STR);
      $sentencia->execute();
      
      $mensaje = "Usuario bloqueado exitosamente.";
      
    } elseif ($accion === 'desbloquear') {
      // Lógica para desbloquear al usuario
      $consultaSQL = "UPDATE users SET bloqueado = 0 WHERE user_name = :user_name";
      $sentencia = $conexion->prepare($consultaSQL);
      $sentencia->bindParam(':user_name', $user_name, PDO::PARAM_STR);
      $sentencia->execute();
      
      $mensaje = "Usuario desbloqueado exitosamente.";
    }
  }

  // Lógica para mostrar la lista de usuarios
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

$roles = [];

try {
    $conexion = conexion();
    
    // Consulta para obtener los roles
    $consultaRoles = "SELECT idRol, rol_descripcion FROM rol";
    $sentenciaRoles = $conexion->prepare($consultaRoles);
    $sentenciaRoles->execute();
    $roles = $sentenciaRoles->fetchAll();

} catch (PDOException $error) {
    $error = $error->getMessage();
}

try {
  $conexion = conexion();

  // Filtros seleccionados
  $rolesSeleccionados = isset($_POST['roles']) ? $_POST['roles'] : [];
  $bloqueadoSeleccionado = isset($_POST['bloqueado']) ? $_POST['bloqueado'] : [];

  // Construir la consulta
  $consultaSQL = "SELECT user_id, user_name, user_email, rol.idRol, rol_descripcion, bloqueado FROM users INNER JOIN rol ON users.idRol = rol.idRol WHERE 1=1";

  // Agregar filtro por roles seleccionados
  if (!empty($rolesSeleccionados)) {
    $rolesParaConsulta = implode(",", array_map('intval', $rolesSeleccionados));
    $consultaSQL .= " AND users.idRol IN ($rolesParaConsulta)";
  }

  // Agregar filtro por estado de bloqueado
  if (!empty($bloqueadoSeleccionado)) {
    $bloqueados = implode(",", array_map('intval', $bloqueadoSeleccionado));
    $consultaSQL .= " AND users.bloqueado IN ($bloqueados)";
  }

  // Filtro por nombre
  if (isset($_POST['apellido']) && !empty($_POST['apellido'])) {
    $consultaSQL .= " AND user_name LIKE '%" . $_POST['apellido'] . "%'";
  }

  // Ejecutar consulta
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
        <a href="noActivos.php" class="btn btn-primary mt-4">No Activos</a>

        <div class="row">
    <div class="col-md-12">

      <!-- Formulario de filtros -->
      <form method="post">
        <div class="form-group" style='margin-top:20px;'>
          <input type="text" id="apellido" name="apellido" placeholder="Buscar por Nombre" class="form-control">
        </div>

       <!-- Menú desplegable para roles -->
      <div class="dropdown" style="margin-top: 20px;">
        <span class="btn btn-secondary">Rol▾</span>
        <div class="dropdown-content">
          <?php foreach ($roles as $rol) : ?>
            <div class="checkbox-wrapper">
              <input type="checkbox" id="rol_<?= escapar($rol['idRol']) ?>" name="roles[]" value="<?= escapar($rol['idRol']) ?>">
              <label for="rol_<?= escapar($rol['idRol']) ?>"><?= escapar($rol['rol_descripcion']) ?></label>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Menú desplegable para bloqueado -->
      <div class="dropdown" style="margin-top: 20px; margin-left: 10px;">
        <span class="btn btn-secondary">Bloqueado▾</span>
        <div class="dropdown-content">
          <div class="checkbox-wrapper">
            <input type="checkbox" id="bloqueado_si" name="bloqueado[]" value="1" <?= in_array(1, $bloqueadoSeleccionado) ? 'checked' : '' ?>>
            <label for="bloqueado_si">Sí</label>
          </div>
          <div class="checkbox-wrapper">
            <input type="checkbox" id="bloqueado_no" name="bloqueado[]" value="0" <?= in_array(0, $bloqueadoSeleccionado) ? 'checked' : '' ?>>
            <label for="bloqueado_no">No</label>
          </div>
        </div>
      </div>

        <!-- Botón de filtrar -->
        <div class="form-group" style="margin-top: 20px; float:right">
          <input type="hidden" name="csrf" value="<?php echo escapar($_SESSION['csrf']); ?>">
          <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
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
                  <button class="boton" onclick="openDeleteModal('<?= escapar($fila['user_name']) ?>')" style='margin-left:10px;'>🗑️ Borrar</button>
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
                        <button class="boton" onclick="openBlockModal('<?= escapar($fila['user_name']) ?>', 'bloquear')" style='margin-left:1px; padding-left: 34px; padding-right: 34px;'>❌ Bloquear</button>
                      <?php else: ?>
                        <button class="boton" onclick="openBlockModal('<?= escapar($fila['user_name']) ?>', 'desbloquear')" style='margin-left:1px;'>✅ Desbloquear</button>
                      <?php endif; ?>
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
                <a href="<?= 'registroUser.php?id=' . escapar($fila["user_id"]) ?>" class="boton">📜</a>
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
        <input type="hidden" name="user_name" id="delete_user_name">
        <input type="hidden" name="accion" value="borrar">
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
    document.getElementById('delete_user_name').value = user_name;
    document.getElementById('deleteModalText').innerText = "¿Estás seguro de que deseas borrar al usuario " + user_name + "?";
    document.getElementById('deleteModal').style.display = "block";
  }

  function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = "none";
  }

  // Funciones para abrir y cerrar el modal de bloquear/desbloquear
  function openBlockModal(user_name, action) {
    document.getElementById('block_user_name').value = user_name;
    document.getElementById('block_action').value = action;
    document.getElementById('blockModalText').innerText = "¿Estás seguro de que deseas " + action + " al usuario " + user_name + "?";
    document.getElementById('blockModal').style.display = "block";
  }

  function closeBlockModal() {
    document.getElementById('blockModal').style.display = "none";
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
