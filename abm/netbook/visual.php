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

$error = false;

// Obtener los tipos de recurso para llenar el selector
$stmtTipos = $conexion->query("SELECT tipo_recurso_id, tipo_recurso_nombre FROM tipo_recurso");
$tipos = $stmtTipos->fetchAll(PDO::FETCH_ASSOC);

// Preparar la consulta para obtener las computadoras según el tipo de recurso seleccionado
$stmtComputadoras = $conexion->prepare("
    SELECT recurso.*, users.user_name
    FROM recurso 
    LEFT JOIN registros 
        ON recurso.recurso_id = registros.idrecurso 
        AND registros.idregistro = (
            SELECT MAX(idregistro) 
            FROM registros AS r
            WHERE r.idrecurso = recurso.recurso_id
        )
    LEFT JOIN users 
        ON registros.idusuario = users.user_id 
    WHERE recurso.recurso_tipo = ?
    ORDER BY recurso.recurso_id
");

include "../template/header.php";
?>

<div class="colores">
    <div>
        <div id="c1"></div>
        <p>Libre</p>
    </div>
    <div>
        <div id="c2"></div>
        <p>Reservado</p>
    </div>
    <div>
        <div id="c3"></div>
        <p>Mantenimiento</p>
    </div>
</div>

<div style='display: flex; justify-content:center;'>
<div id="netbookContainer" style='display: flex; flex-wrap: wrap; width: 1000px;'>
    <div style='background-color: white; display: flex; flex-wrap: wrap; margin-top:35px; margin-left:110px;'>
    
    <form id="miFormulario" style='margin-top:10px;'>
      <label for="opciones">Selecciona una opción:</label>
      <select id="opciones" name="opciones">
        <?php foreach ($tipos as $tipo): ?>
          <option value="<?= $tipo['tipo_recurso_id'] ?>"><?= $tipo['tipo_recurso_nombre'] ?></option>
        <?php endforeach; ?>
      </select>
    </form>

    <div id="computadorasDiv" class="hidden">
        <div style='display:flex; flex-wrap:wrap; background-color: white; border-radius: 10px; margin-top: 25px;' id="netbooks">
            <!-- Las computadoras se cargarán aquí mediante AJAX -->
        </div>
    </div>
</div>

</div>
</div>

<a href="expirados.php" class="boton" title="Muestra las netbooks que han pasado su tiempo de prestamo" style='margin-left:20px;'>Expirados</a>
<a href="graficos.php" class="boton" title="Muestra graficos de los estados de las netbooks" style='margin-left: 78%;'>Estadisticas</a>

<script>
  // Capturar el evento de cambio en el select
  document.getElementById('opciones').addEventListener('change', function() {
    var tipoSeleccionado = this.value;
    mostrarDiv(tipoSeleccionado);
  });

  function mostrarDiv(tipoSeleccionado) {
    var divComputadoras = document.getElementById('computadorasDiv');
    var netbooksContainer = document.getElementById('netbooks');
    netbooksContainer.innerHTML = ''; // Limpiar contenido previo

    // Realizar una solicitud AJAX para obtener las computadoras
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'obtener_computadoras.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
      if (this.status == 200) {
        var response = JSON.parse(this.responseText);
        response.forEach(function(row) {
          var color = row.recurso_estado == '1' ? '#d4edda' : (row.recurso_estado == '2' ? '#f8d7da' : '#fff3cd');
          netbooksContainer.innerHTML += `
            <div class='netbook'
                 data-recurso_id='${row.recurso_id}' 
                 data-recurso_nombre='${row.recurso_nombre}' 
                 data-recurso_estado='${row.recurso_estado}' 
                 data-reservado-por='${row.user_name}' 
                 style='background-color: ${color}; width: 50px; height: 50px; margin: 10px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.15); display: flex; justify-content: center; align-items: center; text-align: center;'>
              <img src='netbook.png' alt='Netbook' style='width: 50%;'>
              <p>${row.recurso_nombre}</p>
            </div>`;
        });
        divComputadoras.classList.remove('hidden'); // Mostrar div con computadoras
      }
    };
    xhr.send('tipo_recurso_id=' + tipoSeleccionado);
  }

  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('opciones').dispatchEvent(new Event('change'));
  });
</script>

<?php include "../template/footer.php"; ?>
</body>
</html>
