
<!-- register form -->
<?php
require_once("..\config\db.php");
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
  <title>Inicio de sesion</title>
</head>

<body>
<form class="form card" method="post" action="verificar_codigo.php" name="verificationform">
        <h1 class="form_heading">Verificacion de Dos pasos</h1>
      </div>
      <div class="field">
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