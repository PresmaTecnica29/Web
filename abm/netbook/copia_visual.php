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
  /* Estilos del modal */
  .modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
  }
  .modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
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
  /* Estilo para los botones */
  .btn {
    display: block;
    width: 150px;
    padding: 10px;
    margin: 20px auto;
    border: none;
    border-radius: 5px;
    background-color: #007bff;
    color: white;
    font-size: 16px;
    cursor: pointer;
    text-align: center;
  }
  .btn:hover {
    background-color: #0056b3;
  }
  #netbookContainer {
    display: flex;
    flex-wrap: wrap;
    width: 1000px;
    margin: 0 auto;
  }
  .netbook {
    background-color: white;
    width: 100px;
    height: 100px;
    margin: 10px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.15);
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
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
                        <img src='netbook.png' alt='Netbook' style='width: 50%;'>
                        <p>{$row['recurso_nombre']}</p>
                      </div>";
            }
            ?>
        </div>   
    </div>
</div>

<!-- Botón para abrir el modal -->
<button id="openModalMostrarDatos" class="btn">Mostrar Datos</button>

<!-- Modal: Mostrar Datos -->
<div id="modalMostrarDatos" class="modal">
  <div class="modal-content">
    <span class="close closeMostrarDatos">&times;</span>
    <h2>Datos de la Consulta</h2>
    <div id="modalOpcion1Div">
      <div style="display:flex; flex-wrap:wrap; background-color: white; border-radius: 10px; margin-top: 25px;">
        <?php 
        $stmt->execute(); // Volver a ejecutar la consulta para el modal
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
                     style='background-color: {$color};'>
                    <img src='netbook.png' alt='Netbook' style='width: 50%;'>
                    <p>{$row['recurso_nombre']}</p>
                  </div>";
        }
        ?>
      </div>
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

  var modalMostrarDatos = document.getElementById("modalMostrarDatos");
  var btnMostrarDatos = document.getElementById("openModalMostrarDatos");
  var spanCerrarMostrarDatos = document.getElementsByClassName("closeMostrarDatos")[0];

  btnMostrarDatos.onclick = function() {
    modalMostrarDatos.style.display = "block";
  }

  spanCerrarMostrarDatos.onclick = function() {
    modalMostrarDatos.style.display = "none";
  }

  window.onclick = function(event) {
    if (event.target == modalMostrarDatos) {
      modalMostrarDatos.style.display = "none";
    }
  }
</script>

<?php include "../template/footer.php"; ?>
</body>
</html>