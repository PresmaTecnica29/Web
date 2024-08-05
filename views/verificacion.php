
<!-- register form -->
<?php
require_once("C:\wamp64\www\Web\config\db.php");
$conexion = conexion();

$datos = []; // Valor predeterminado

if ($conexion) {
  $statement = $conexion->prepare("SELECT `idRol`, `rol_descripcion` FROM `rol` where rol_descripcion <> 'Administrador' and rol_descripcion <> 'Alumno'");
  $statement->execute();
  $datos = $statement->fetchAll(); // Actualiza $datos si la conexión es exitosa
} else {
  echo "Error: No se pudo conectar a la base de datos.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="views\estilo.css">
  <link rel="icon" href="views/templates/logofinal.png" type="image/png">
  <title>Inico de sesion</title>
</head>

<body>
  <div id="formulario">
    <form class="form card" method="post" action="register.php" name="registerform">
      <div class="card_header">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
          <path fill="none" d="M0 0h24v24H0z"></path>
          <path fill="currentColor" d="M4 15h2v5h12V4H6v5H4V3a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6zm6-4V8l5 4-5 4v-3H2v-2h8z"></path>
        </svg>
        <h1 class="form_heading">Verificacion de Dos pasos</h1>
      </div>
      <div class="field">
        <label for="verfcode">verfcode</label>
        <input id="registerinput_verfcode" class="input" type="int(6)" placeholder="Codigo de verificacion" name="verfcode_input" />
      </div>
      <div class="field">
        <input type="submit" name="Comprobar" value="Codigo" class="input" />
      </div>
      <a href="index.php" id="back">Volver a la pagina de inicio de sesion</a>
    </form>
  </div>
</body>

</html>