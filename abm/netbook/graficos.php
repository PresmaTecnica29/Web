<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Computadoras</title>
<link rel="stylesheet" type="text/css" href="style.css">
<style>
  /* Estilos opcionales para los divs */
  .hidden {
    display: none;
  }
  .close:hover,
  .close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
  }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php
$config = include('../../config/db.php');
$conexion = conexion();
include '../funciones.php';

csrf();
if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
    die();
}

try {
    // Conectar a la base de datos
    $conexion = conexion();

    // Consulta SQL para contar cuántas veces se repiten los números 1, 2 y 3 en el campo recurso_estado
    // solo para registros cuyo recurso_id termina en 'A', 'B' y 'C'
    $consultaSQL = "
        SELECT 
            CASE 
                WHEN recurso_id LIKE '%A' THEN 'A'
                WHEN recurso_id LIKE '%B' THEN 'B'
                WHEN recurso_id LIKE '%C' THEN 'C'
            END AS recurso_id_suffix,
            recurso_estado,
            COUNT(*) AS total
        FROM recurso
        WHERE recurso_id LIKE '%A' OR recurso_id LIKE '%B' OR recurso_id LIKE '%C'
        AND recurso_estado IN (1, 2, 3)
        GROUP BY recurso_id_suffix, recurso_estado
        ORDER BY recurso_id_suffix, recurso_estado
    ";

    // Preparar y ejecutar la consulta
    $sentencia = $conexion->prepare($consultaSQL);
    $sentencia->execute();

    // Obtener los resultados
    $resultados = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    // Inicializar variables para contar las ocurrencias
    $countA1 = $countA2 = $countA3 = 0;
    $countB1 = $countB2 = $countB3 = 0;
    $countC1 = $countC2 = $countC3 = 0;

    // Guardar los resultados en las variables
    foreach ($resultados as $resultado) {
        $sufijo = $resultado['recurso_id_suffix'];
        $estado = $resultado['recurso_estado'];
        $total = $resultado['total'];

        switch ($sufijo) {
            case 'A':
                if ($estado == 1) $countA1 = $total;
                if ($estado == 2) $countA2 = $total;
                if ($estado == 3) $countA3 = $total;
                break;
            case 'B':
                if ($estado == 1) $countB1 = $total;
                if ($estado == 2) $countB2 = $total;
                if ($estado == 3) $countB3 = $total;
                break;
            case 'C':
                if ($estado == 1) $countC1 = $total;
                if ($estado == 2) $countC2 = $total;
                if ($estado == 3) $countC3 = $total;
                break;
        }
    }

} catch (PDOException $error) {
    // Manejo de errores
    echo "Error: " . $error->getMessage();
}

include "../template/header.php";
?>

<form id="miFormulario" style='margin-top: 20px; margin-left: 100px;'>
  <label for="opciones">Selecciona una opción:</label>
  <select id="opciones" name="opciones">
    <option value="opcion1">Carrito 1</option>
    <option value="opcion2">Carrito 2</option>
    <option value="opcion3">Carrito 3</option>
  </select>
</form>

<div id="opcion1Div" class="hidden">
    <div style='display: block; margin-left: 500px;'>
        <div style='width: 500px; height: 500px; display: block;'>
        <h2 style='margin-left: 50px;'>Computadoras del Carrito 1</h2>
        <canvas id="graficoCarrito1"></canvas>
        <script>
            var datosCarrito1 = {
                labels: ['Libres', 'Ocupadas', 'En Mantenimiento'],
                datasets: [{
                    label: 'Conteo por Estado',
                    data: [<?php echo $countA1; ?>, <?php echo $countA2; ?>, <?php echo $countA3; ?>],
                    backgroundColor: ['#4fe573', '#f3505f', '#ffd652'],
                    hoverOffset: 4
                }]
            };

            var ctxCarrito1 = document.getElementById('graficoCarrito1').getContext('2d');
            new Chart(ctxCarrito1, {
                type: 'pie',
                data: datosCarrito1
            });
        </script>
        </div>
    </div>
</div>

<div id="opcion2Div" class="hidden">
    <div style='display: block; margin-left: 500px;'>
        <div style='width: 500px; height: 500px; display: block;'>
        <h2 style='margin-left: 50px;'>Computadoras del Carrito 2</h2>
        <canvas id="graficoCarrito2"></canvas>
        <script>
            var datosCarrito2 = {
                labels: ['Libres', 'Ocupadas', 'En Mantenimiento'],
                datasets: [{
                    label: 'Conteo por Estado',
                    data: [<?php echo $countB1; ?>, <?php echo $countB2; ?>, <?php echo $countB3; ?>],
                    backgroundColor: ['#4fe573', '#f3505f', '#ffd652'],
                    hoverOffset: 4
                }]
            };

            var ctxCarrito2 = document.getElementById('graficoCarrito2').getContext('2d');
            new Chart(ctxCarrito2, {
                type: 'pie',
                data: datosCarrito2
            });
        </script>
        </div>
     </div>   
</div>

<div id="opcion3Div" class="hidden">
    <div style='display: block; margin-left: 500px;'>
        <div style='width: 500px; height: 500px; display: block;'>
        <h2 style='margin-left: 50px;'>Computadoras del Carrito 3</h2>
        <canvas id="graficoCarrito3"></canvas>
        <script>
            var datosCarrito3 = {
                labels: ['Libres', 'Ocupadas', 'En Mantenimiento'],
                datasets: [{
                    label: 'Conteo por Estado',
                    data: [<?php echo $countC1; ?>, <?php echo $countC2; ?>, <?php echo $countC3; ?>],
                    backgroundColor: ['#4fe573', '#f3505f', '#ffd652'],
                    hoverOffset: 4
                }]
            };

            var ctxCarrito3 = document.getElementById('graficoCarrito3').getContext('2d');
            new Chart(ctxCarrito3, {
                type: 'pie',
                data: datosCarrito3
            });
        </script>
        </div>
    </div>
</div>

<script>
  // Capturar el evento de cambio en el select
  document.getElementById('opciones').addEventListener('change', function() {
    var seleccion = document.getElementById('opciones').value;
    if (seleccion === 'opcion1') {
      mostrarDiv('opcion1Div');
    } else if (seleccion === 'opcion2') {
      mostrarDiv('opcion2Div');
    } else if (seleccion === 'opcion3') {
      mostrarDiv('opcion3Div');
    }
  });

  function mostrarDiv(idDiv) {
    var divs = document.querySelectorAll('div[id$="Div"]');
    divs.forEach(function(div) {
      div.classList.add('hidden');
    });
    var divMostrar = document.getElementById(idDiv);
    divMostrar.classList.remove('hidden');
  }

  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('opciones').value = 'opcion1';
    document.getElementById('opciones').dispatchEvent(new Event('change'));
  });
</script>

<a href="visual.php" class="boton" style='margin-left:20px;'>Volver Atras</a>


<?php include "../template/footer.php"; ?>

</body>
</html>