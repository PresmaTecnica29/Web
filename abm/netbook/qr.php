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
      min-width: 360px;
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

    // Construcción de la consulta SQL
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
                    WHERE 1=1";

    // Filtrar por ID si se proporciona
    if (isset($_POST['apellido']) && !empty($_POST['apellido'])) {
      $consultaSQL .= " AND recurso.recurso_id LIKE '%" . $_POST['apellido'] . "%'";
    }

    // Filtrar por estado
    if (isset($_POST['estado']) && !empty($_POST['estado'])) {
      $estadosSeleccionados = implode(",", $_POST['estado']);
      $consultaSQL .= " AND recurso.recurso_estado IN ($estadosSeleccionados)";
    }

    // Filtrar por tipos de recurso
    if (isset($_POST['tipo_recurso']) && !empty($_POST['tipo_recurso'])) {
      $tiposSeleccionados = implode(",", $_POST['tipo_recurso']);
      $consultaSQL .= " AND recurso.recurso_tipo IN ($tiposSeleccionados)";
  }

  // Filtrar por áreas
  if (isset($_POST['area']) && !empty($_POST['area'])) {
      $areasSeleccionadas = implode(",", $_POST['area']);
      $consultaSQL .= " AND area.id IN ($areasSeleccionadas)";
  }
  
    $consultaSQL .= " LIMIT 100;";

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

  // Consulta para obtener los tipos de recursos
$tiposRecursos = [];
$areas = [];
try {
    // Tipos de Recursos
    $sqlTiposRecursos = "SELECT tipo_recurso_id, tipo_recurso_nombre FROM tipo_recurso";
    $stmtTiposRecursos = $conexion->prepare($sqlTiposRecursos);
    $stmtTiposRecursos->execute();
    $tiposRecursos = $stmtTiposRecursos->fetchAll();

    // Áreas
    $sqlAreas = "SELECT id, area_nombre FROM area";
    $stmtAreas = $conexion->prepare($sqlAreas);
    $stmtAreas->execute();
    $areas = $stmtAreas->fetchAll();
} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
}
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
        <a href="areas.php" class="btn btn-primary mt-4" style="margin-left: 10px;">Areas</a>

        <!-- Formulario de búsqueda y filtros -->
        <form method="post" class="form-inline">
          <div class="form-group mr-3" style='margin-top:20px;'>
            <input type="text" id="apellido" name="apellido" placeholder="Buscar por Id" class="form-control">
          </div>

          <!-- Menú desplegable para filtros por estado -->
          <div class="dropdown" style="margin-top: 20px; margin-bottom: 10px;">
            <span class="btn btn-secondary">Estado▾</span>
            <div class="dropdown-content">
              <div class="checkbox-wrapper">
                <input type="checkbox" id="libre" name="estado[]" value="1">
                <label for="libre">Libre</label>
              </div>
              <div class="checkbox-wrapper">
                <input type="checkbox" id="reservado" name="estado[]" value="2">
                <label for="reservado">Reservado</label>
              </div>
              <div class="checkbox-wrapper">
                <input type="checkbox" id="mantenimiento" name="estado[]" value="3">
                <label for="mantenimiento">En Mantenimiento</label>
              </div>
            </div>
          </div>

          <!-- Menú desplegable para tipos de recurso -->
          <div class="dropdown" style="margin-top: 20px; margin-left: 10px;">
            <span class="btn btn-secondary">Tipo de Recurso▾</span>
            <div class="dropdown-content">
              <?php foreach ($tiposRecursos as $tipo) : ?>
                <div class="checkbox-wrapper">
                  <input type="checkbox" id="tipo_<?= escapar($tipo['tipo_recurso_id']) ?>" name="tipo_recurso[]" value="<?= escapar($tipo['tipo_recurso_id']) ?>">
                  <label for="tipo_<?= escapar($tipo['tipo_recurso_id']) ?>"><?= escapar($tipo['tipo_recurso_nombre']) ?></label>
                </div>
              <?php endforeach; ?>
            </div>
          </div>


          <!-- Menú desplegable para áreas -->
          <div class="dropdown" style="margin-top: 20px; margin-left: 10px;">
            <span class="btn btn-secondary">Area▾</span>
            <div class="dropdown-content">
              <?php foreach ($areas as $area) : ?>
                <div class="checkbox-wrapper">
                  <input type="checkbox" id="area_<?= escapar($area['id']) ?>" name="area[]" value="<?= escapar($area['id']) ?>">
                  <label for="area_<?= escapar($area['id']) ?>"><?= escapar($area['area_nombre']) ?></label>
                </div>
              <?php endforeach; ?>
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
                        $estadoRecurso = isset($fila["recurso_estado"]) ? $fila["recurso_estado"] : null;

                        if ($estadoRecurso == 1 || $estadoRecurso == 2) {
                          ?>
                          <button class="boton" onclick="openModal('<?= escapar($fila['recurso_nombre']) ?>', 'mantenimiento')" style='margin-left:10px;'>Poner en Mantenimiento</button>
                          <?php
                        } elseif ($estadoRecurso == 3) {
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
      <form id="modalForm" method="POST" action="">
        <input type="hidden" name="recurso_nombre" id="recurso_nombre">
        <input type="hidden" name="accion" id="accion">
        <input type="hidden" name="csrf" value="<?php echo escapar($_SESSION['csrf']); ?>">
        <button type="submit" class="btn btn-danger" style="background-color: red;" id="confirmButton">Si</button>
        <button type="button" class="btn btn-secondary" onclick="closeModal()">No</button>
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