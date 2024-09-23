<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Recursos</title>
  <link rel="stylesheet" type="text/css" href="style.css">
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

<body>

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

    // Consulta SQL
    if (isset($_POST['apellido'])) {
      $consultaSQL = "SELECT 
                            recurso.recurso_id, 
                            recurso.recurso_nombre, 
                            tipo_recurso.tipo_recurso_nombre, 
                            area.area_nombre, 
                            estado.descripcion_estado, 
                            recurso.recurso_estado
                        FROM recurso 
                        INNER JOIN tipo_recurso ON recurso.recurso_tipo = tipo_recurso.tipo_recurso_id 
                        INNER JOIN area ON tipo_recurso.tipo_recurso_area = area.id 
                        INNER JOIN estado ON recurso.recurso_estado = estado.idEstado 
                        WHERE recurso.recurso_id LIKE '%" . $_POST['apellido'] . "%' 
                        LIMIT 100;";
    } else {
      $consultaSQL = "SELECT 
                            recurso.recurso_id, 
                            recurso.recurso_nombre, 
                            tipo_recurso.tipo_recurso_nombre, 
                            area.area_nombre, 
                            estado.descripcion_estado, 
                            recurso.recurso_estado
                        FROM recurso 
                        INNER JOIN tipo_recurso ON recurso.recurso_tipo = tipo_recurso.tipo_recurso_id 
                        INNER JOIN area ON tipo_recurso.tipo_recurso_area = area.id 
                        INNER JOIN estado ON recurso.recurso_estado = estado.idEstado;";
    }

    // Preparar y ejecutar la consulta
    $sentencia = $conexion->prepare($consultaSQL);
    $sentencia->execute();

    // Obtener los resultados
    $alumnos = $sentencia->fetchAll();
  } catch (PDOException $error) {
    $error = $error->getMessage();
  }

  // Título según si hay un valor de búsqueda o no
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
        <?php
        if (isset($_SESSION['user_rol'])) {
          if ($_SESSION['user_rol'] == 5) {
            echo '<a href="agregarMaterial.php" class="btn btn-primary mt-4">Agregar material</a>';
          }
        }
        ?>
        <a href="tiposMateriales.php" class="btn btn-primary mt-4" style="margin-left: 10px;">Tipos de Materiales</a>
        <a href="areas.php" class="btn btn-primary mt-4" style="margin-left: 10px;">Áreas</a>

        <form method="post" class="form-inline">
          <div class="form-group mr-3" style='margin-top:20px;'>
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
              <th>ID</th>
              <th>Material</th>
              <th>Tipo de Recurso</th>
              <th>Estado</th>
              <th>Área</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($alumnos && $sentencia->rowCount() > 0) {
              foreach ($alumnos as $fila) {
                ?>
                <tr>
                  <td><?php echo escapar($fila["recurso_id"]); ?></td>
                  <td><?php echo escapar($fila["recurso_nombre"]); ?></td>
                  <td><?php echo escapar($fila["tipo_recurso_nombre"]); ?></td>
                  <td><?php echo escapar($fila["descripcion_estado"]); ?></td>
                  <td><?php echo escapar($fila["area_nombre"]); ?></td>
                  <td>
                    <a href="<?= 'generar_qr.php?id=' . escapar($fila["recurso_id"] . '&nombre=' . escapar(($fila["recurso_nombre"]))) ?>"
                       class="boton" title="Crea un nuevo QR para este recurso" style='margin-left:10px;'>Generar Qr</a>
                    <a href="<?= 'abrirqr.php?nombre=' . escapar($fila["recurso_nombre"]) ?>" class="boton"
                       title="Muestra el QR actual de este recurso" style='margin-left:10px;'>Abrir Qr</a>

                    <?php
                    if (isset($_SESSION['user_rol'])) {
                      if ($_SESSION['user_rol'] == 5 || $_SESSION['user_rol'] == 4 || $_SESSION['user_rol'] == 3) {
                        // Acciones según el estado del recurso
                        $estadoRecurso = isset($fila["recurso_estado"]) ? $fila["recurso_estado"] : null;

                        // Poner en mantenimiento
                        if ($estadoRecurso == 1 || $estadoRecurso == 2) {
                          ?>
                          <button class="boton" onclick="openModal('<?= escapar($fila['recurso_nombre']) ?>', 'mantenimiento')" style='margin-left:10px;'>Poner en Mantenimiento</button>
                          <?php
                        } elseif ($estadoRecurso == 3) { // Habilitar
                          ?>
                          <button class="boton" onclick="openModal('<?= escapar($fila['recurso_nombre']) ?>', 'habilitar')" style='margin-left:10px;'>Habilitar</button>
                          <?php
                        }
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

  <!-- Modal -->
  <div id="modal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <p id="modalText"></p>
      <form id="modalForm" method="post">
        <input type="hidden" name="recurso_nombre" id="recurso_nombre">
        <input type="hidden" name="accion" id="accion">
        <input type="hidden" name="csrf" value="<?php echo escapar($_SESSION['csrf']); ?>">
        <button type="submit" class="btn btn-primary" style="margin-top: 20px; margin-right: 20px; background-color: green; padding-left: 20px; padding-right: 20px;">Sí</button>
        <button type="button" class="btn btn-secondary" onclick="closeModal()" style="margin-top: 20px; margin-left: 20px; background-color: red; padding-left: 20px; padding-right: 20px;">No</button>
      </form>
    </div>
  </div>

  <script>
    function openModal(nombre, accion) {
      document.getElementById("modalText").innerText = "¿Estás seguro de que quieres " + (accion === "mantenimiento" ? "poner en mantenimiento" : "habilitar") + " el material " + nombre + "?";
      document.getElementById("recurso_nombre").value = nombre;
      document.getElementById("accion").value = accion;
      document.getElementById("modal").style.display = "block";
    }

    function closeModal() {
      document.getElementById("modal").style.display = "none";
    }
    
    // Cerrar el modal al hacer clic fuera de él
    window.onclick = function(event) {
      if (event.target === document.getElementById("modal")) {
        closeModal();
      }
    }
  </script>

  <?php
  // Procesar el formulario del modal
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recurso_nombre']) && isset($_POST['accion'])) {
    $nombreRecurso = $_POST['recurso_nombre'];
    $accion = $_POST['accion'];

    try {
      $conexion = conexion();
      if ($accion === 'mantenimiento') {
        $stmt = $conexion->prepare("UPDATE recurso SET recurso_estado = '3' WHERE recurso_nombre = ?");
      } else if ($accion === 'habilitar') {
        $stmt = $conexion->prepare("UPDATE recurso SET recurso_estado = '1' WHERE recurso_nombre = ?");
      }
      $stmt->execute([$nombreRecurso]);
      // Solo recargar la página una vez después de la acción
    } catch (PDOException $e) {
      echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
  }
  ?>

  <?php include "../template/footer.php"; ?>

</body>
</html>
