<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Computadoras</title>
<style>
  /* Estilos opcionales para los divs */
  .hidden {
    display: none;
  }
</style>
</head>

<?php
$config = include('../../config/db.php');
$conexion = conexion();
include '../funciones.php';

csrf();
if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
    die();
}

$error = false;


$stmt = $conexion->query("
SELECT *

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
WHERE recurso.recurso_id LIKE '%A'
ORDER BY recurso.recurso_id

");
$stmtB = $conexion->query("
SELECT *
    
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
WHERE recurso.recurso_id LIKE '%B'
ORDER BY recurso.recurso_id

");
$stmtC = $conexion->query("
SELECT *

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
WHERE recurso.recurso_id LIKE '%C'
ORDER BY recurso.recurso_id

");



?>

<?php include "../template/header.php"; ?>

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
        <option value="opcion1">Carrito 1</option>
        <option value="opcion2">Carrito 2</option>
        <option value="opcion3">Carrito 3</option>
      </select>
    </form>

    <div id="opcion1Div" class="hidden">
        <div style='display:flex; flex-wrap:wrap; background-color: white; border-radius: 10px; margin-top: 25px;'>
            <?php 
            while ($row = $stmt->fetch()) {
                if ($row['recurso_estado'] == '1') {
                    $color = '#d4edda'; // Verde claro para "Libre"
                } elseif ($row['recurso_estado'] == '2') {
                    $color = '#f8d7da'; // Rojo claro para "Ocupado"
                } elseif ($row['recurso_estado'] == '3') {
                    $color = '#fff3cd'; // Amarillo claro para "Reservado"
                } 
                echo "<div class='netbook'
                         data-recurso_id='{$row['recurso_id']}' 
                         data-recurso_nombre='{$row['recurso_nombre']}' 
                         data-recurso_estado='{$row['recurso_estado']}' 
                         data-reservado-por='{$row['user_name']}' 
                         style='background-color: {$color}; width: 50px; height: 50px; margin: 10px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.15); display: flex; justify-content: center; align-items: center; text-align: center;'>
                        <img src='netbook.png' alt='Netbook' style='width: 50%;'>
                        <p>{$row['recurso_nombre']}</p>
                      </div>";
            }
            ?>
        </div>
    </div>

    <div id="opcion2Div" class="hidden">
        <div style='display:flex; flex-wrap:wrap; background-color: white; border-radius: 10px; margin-top: 25px;'>
            <?php 
            while ($row = $stmtB->fetch()) {
                if ($row['recurso_estado'] == '1') {
                    $color = '#d4edda'; // Verde claro para "Libre"
                } elseif ($row['recurso_estado'] == '2') {
                    $color = '#f8d7da'; // Rojo claro para "Ocupado"
                } elseif ($row['recurso_estado'] == '3') {
                    $color = '#fff3cd'; // Amarillo claro para "Reservado"
                } 
                echo "<div class='netbook'
                         data-recurso_id='{$row['recurso_id']}' 
                         data-recurso_nombre='{$row['recurso_nombre']}' 
                         data-recurso_estado='{$row['recurso_estado']}' 
                         data-reservado-por='{$row['user_name']}' 
                         style='background-color: {$color}; width: 50px; height: 50px; margin: 10px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.15); display: flex; justify-content: center; align-items: center; text-align: center;'>
                        <img src='netbook.png' alt='Netbook' style='width: 50%;'>
                        <p>{$row['recurso_nombre']}</p>
                      </div>";
            }
            ?>
        </div>   
    </div>

    <div id="opcion3Div" class="hidden">
    <div style='display:flex; flex-wrap:wrap; background-color: white; border-radius: 10px; margin-top: 25px;'>
            <?php 
            while ($row = $stmtC->fetch()) {
                if ($row['recurso_estado'] == '1') {
                    $color = '#d4edda'; // Verde claro para "Libre"
                } elseif ($row['recurso_estado'] == '2') {
                    $color = '#f8d7da'; // Rojo claro para "Ocupado"
                } elseif ($row['recurso_estado'] == '3') {
                    $color = '#fff3cd'; // Amarillo claro para "Reservado"
                } 
                echo "<div class='netbook'
                         data-recurso_id='{$row['recurso_id']}' 
                         data-recurso_nombre='{$row['recurso_nombre']}' 
                         data-recurso_estado='{$row['recurso_estado']}' 
                         data-reservado-por='{$row['user_name']}' 
                         style='background-color: {$color}; width: 50px; height: 50px; margin: 10px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.15); display: flex; justify-content: center; align-items: center; text-align: center;'>
                        <img src='netbook.png' alt='Netbookkkkkkkk' style='width: 50%;'>
                        <p>{$row['recurso_nombre']}</p>
                      </div>";
            }
            ?>
        </div>   
    </div>

    <script>
      // Capturar el evento de cambio en el select
      document.getElementById('opciones').addEventListener('change', function() {
        // Obtener el valor seleccionado en el select
        var seleccion = document.getElementById('opciones').value;

        // Mostrar el div correspondiente según la opción seleccionada
        if (seleccion === 'opcion1') {
          mostrarDiv('opcion1Div');
        } else if (seleccion === 'opcion2') {
          mostrarDiv('opcion2Div');
        } else if (seleccion === 'opcion3') {
          mostrarDiv('opcion3Div');
        }
      });

      // Función para mostrar el div deseado y ocultar los demás
      function mostrarDiv(idDiv) {
        // Ocultar todos los divs
        var divs = document.querySelectorAll('div[id$="Div"]');
        divs.forEach(function(div) {
          div.classList.add('hidden');
        });

        // Mostrar el div especificado
        var divMostrar = document.getElementById(idDiv);
        divMostrar.classList.remove('hidden');
      }

      // Ejecutar cuando la página termine de cargarse
      document.addEventListener('DOMContentLoaded', function() {
        // Simular el cambio para la primera opción (opcion1)
        document.getElementById('opciones').value = 'opcion1';
        document.getElementById('opciones').dispatchEvent(new Event('change'));
      });
    </script>

    </div>
</div>


</div>

<?php include "../template/footer.php"; ?>