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
        $consultaSQL = "SELECT * FROM area";

    // Preparar y ejecutar la consulta
    $sentencia = $conexion->prepare($consultaSQL);
    $sentencia->execute();

    // Obtener los resultados
    $alumnos = $sentencia->fetchAll();
  } catch (PDOException $error) {
    $error = $error->getMessage();
  }

  // Título según si hay un valor de búsqueda o no
  $titulo = isset($_POST['apellido']) ? 'Lista de Areas (' . $_POST['apellido'] . ')' : 'Lista de Areas';
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
              echo '<a href="agregarArea.php" class="btn btn-primary mt-4" style="margin-left: 10px;">Agregar Area</a>';
            }
          } ?>
        <a href="qr.php" class="btn btn-primary mt-4" style="margin-left: 10px;">Recursos</a>
        <a href="tiposMateriales.php" class="btn btn-primary mt-4" style="margin-left: 10px;">Tipos de Materiales</a>

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
              <th>Area</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($alumnos && $sentencia->rowCount() > 0) {
              foreach ($alumnos as $fila) {
                ?>
                <tr>
                  <td><?php echo escapar($fila["id"]); ?></td>
                  <td><?php echo escapar($fila["area_nombre"]); ?></td>
                  <td>
                  <?php
          if (isset($_SESSION['user_rol'])) {
              if ($_SESSION['user_rol'] == 5) {
                  // Solo se ejecutará este código si el rol del usuario es 5 
                  ?>
                  <a href="<?= 'editarArea.php?id=' . escapar($fila["id"]) ?>" class="boton"
                  title="Cambia el estado del recurso a MANTENIMIENTO" style='margin-left:10px;'>✏️ Editar</a>
                  <?php
                    }
                  }
                ?> 
                <?php
                if (isset($_SESSION['user_rol'])) {
                    if ($_SESSION['user_rol'] == 5) {
                        // Solo se ejecutará este código si el rol del usuario es 5 
                        ?>
                        <a href="<?= 'borrarArea.php?id=' . escapar($fila["id"]) ?>" class="boton"
                        title="Cambia el estado del recurso a MANTENIMIENTO" style='margin-left:10px;'>🗑️ Borrar</a>
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

  <?php include "../template/footer.php"; ?>

</body>
</html>
