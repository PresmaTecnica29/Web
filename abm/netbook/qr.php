<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Estado</title>
    <script>
        function cambiarEstado(netbookId) {
            fetch('?' + new URLSearchParams({
                action: 'cambiar_estado',
                id: netbookId,
                estado: 3
            }), {
                method: 'GET'
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                console.log(data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
</head>
<body>

    <?php
    // Verifica si se ha enviado una solicitud para cambiar el estado
    if (isset($_GET['action']) && $_GET['action'] == 'cambiar_estado') {
        
        // Obtener datos del GET
        $id = intval($_GET['id']);
        $estado = intval($_GET['estado']);

        // Preparar y ejecutar la consulta SQL
        $sql = "UPDATE netbooks SET recurso_estado = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $estado, $id);

        if ($stmt->execute()) {
            echo "Estado cambiado exitosamente";
        } else {
            echo "Error al cambiar el estado: " . $conn->error;
        }
        // Terminar el script para evitar que se ejecute el HTML después de la solicitud AJAX
        exit();
    }
    ?>
    
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
    $consultaSQL = "SELECT recurso.recurso_id, recurso.recurso_nombre, estado.descripcion_estado, area.area_nombre FROM recurso INNER JOIN area ON recurso.recurso_tipo = area.id inner join estado on recurso.recurso_estado = estado.idEstado AND recurso.recurso_id LIKE '%" . $_POST['apellido'] . "%' limit 100;";
  } else {
    $consultaSQL = "SELECT recurso.recurso_id, recurso.recurso_nombre, estado.descripcion_estado, area.area_nombre FROM recurso INNER JOIN area ON recurso.recurso_tipo = area.id inner join estado on recurso.recurso_estado = estado.idEstado;";
  }

  $sentencia = $conexion->prepare($consultaSQL);
  $sentencia->execute();

  $alumnos = $sentencia->fetchAll();
} catch (PDOException $error) {
  $error = $error->getMessage();
}

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
    
      <a href="agregarMaterial.php" class="btn btn-primary mt-4">Agregar material</a>
      <a href="visual.php" class="btn btn-primary mt-4">Forma visual</a>
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
            <th>#</th>
            <th>Material</th>
            <th>Estado</th>
            <th>Area</th>
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
                <td><?php echo escapar($fila["descripcion_estado"]); ?></td>
                <td><?php echo escapar($fila["area_nombre"]); ?></td>
                <td>
                  <a href="<?= 'generar_qr.php?id=' . escapar($fila["recurso_id"] . '&nombre=' . escapar(($fila["recurso_nombre"]))) ?>">Generar Qr</a>
                  <a href="<?= 'abrirqr.php?nombre=' . escapar($fila["recurso_nombre"]) ?>">Abrir Qr</a>

                  <!-- Botón para cambiar el estado de una netbook con ID 1 -->
                  <button onclick="cambiarEstado(1)">Poner en Mantenimiento</button>
                  
                </td>
              </tr>
          <?php
          
            }
          }
          ?>
        </body>
      </table>
    </div>
  </div>
</div>



<?php include "../template/footer.php"; ?>