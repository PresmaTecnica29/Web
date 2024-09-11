<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Recursos</title>
  <link rel="stylesheet" type="text/css" href="style.css">
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
                            recurso.recurso_estado -- Agregar la columna recurso_estado
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
                            recurso.recurso_estado -- Agregar la columna recurso_estado
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
          } ?>
        <hr>

        <form method="post" class="form-inline">
          <div class="form-group mr-3">
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

                        // Manejar el caso donde recurso_estado pueda ser nulo o no esté definido
                        $estadoRecurso = isset($fila["recurso_estado"]) ? $fila["recurso_estado"] : null;

                        if ($estadoRecurso == 1 || $estadoRecurso == 2) {
                          ?>
                          <a href="<?= 'mantenimientonetbook.php?nombre=' . escapar($fila["recurso_nombre"]) ?>" class="boton"
                            title="Cambia el estado del recurso a MANTENIMIENTO" style='margin-left:10px;'>Poner en Mantenimiento</a>
                          <?php
                        } elseif ($estadoRecurso == 3) {
                          ?>
                          <a href="<?= 'habilitarnetbook.php?nombre=' . escapar($fila["recurso_nombre"]) ?>" class="boton"
                            title="Cambia el estado del recurso a LIBRE" style='margin-left:10px;'>Habilitar</a>
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

  <?php include "../template/footer.php"; ?>

</body>
</html>
