
<!-- register form -->
<?php
require "../classes/enviar_gmails.php";
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

// Depuración: Ver el contenido de $_POST
echo "<pre>";
print_r($_POST);  // Muestra todo el arreglo $_POST
echo "</pre>";
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
</form>
<body>  
<div id="formulario">
  <form id="formverificar" class="form card" method="post" action="verificar_codigo.php" name="verfform">
      <div class="card_header">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
          <path fill="none" d="M0 0h24v24H0z"></path>
          <path fill="currentColor" d="M4 15h2v5h12V4H6v5H4V3a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6zm6-4V8l5 4-5 4v-3H2v-2h8z"></path>
        </svg>
        <h1 class="form_heading">Verificaracion</h1>
      </div>
      <div class="field">
        <label for="login_input_email">Email del usuario</label>
        <input type="text" name="user_email" class="input" placeholder="example@tecnica29de6.edu.ar" value="<?php echo htmlspecialchars($_POST['user_email'] ?? ''); ?>" />
      </div>
      <div class="field">
      <label for="login_input_email">Codigo de verificacion</label>
            <input id="registerinput_verfcode" class="input" type="text"placeholder="Codigo de verificacion" name="verfcode_input" required />
        </div>
      <div class="field">
        <input type="submit" name="register" value="Submit" class="input"/>
      </div>
      <a href="index.php" id="back">Volver a la pagina de inicio de sesion</a>
      </form>
    </form>
  </div>
</body>
</html>
<style>
html,
body {
    height: 100%;
    background-image: linear-gradient(135deg, #87b5ff 10%, #a1e1ff 100%);
}

form {
    height: 60%;
    width: 30%;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    display: flex;
    border-radius: 5px;
    background-color: rgb(245, 246, 247);
}

#formulario {
    display: flex;
    height: 100%;
    width: 100%;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}

#nom {
    font-size: 29px;
    margin-top: 30px;
}


#submit {
    margin-top: 30px;
}

.card {
    width: 250px;
    background: #F4F6FB;
    border: 1px solid white;
    box-shadow: 10px 10px 64px 0px rgba(180, 180, 207, 0.75);
    -webkit-box-shadow: 10px 10px 64px 0px rgba(186, 186, 202, 0.75);
    -moz-box-shadow: 10px 10px 64px 0px rgba(208, 208, 231, 0.75);
}

.form {
    padding: 25px;
}

.card_header {
    display: flex;
    align-items: center;
}

.card svg {
    color: #7878bd;
    margin-bottom: 20px;
    margin-right: 5px;
}

.form_heading {
    padding-bottom: 20px;
    font-size: 21px;
    color: #7878bd;
}

.field {
    padding-bottom: 10px;
}

.input {
    border-radius: 5px;
    background-color: #e9e9f7;
    padding: 5px;
    width: 100%;
    color: #7a7ab3;
    border: 1px solid #dadaf7
}


.input:focus-visible {
    outline: 1px solid #aeaed6;
}

.input::placeholder {
    color: #bcbcdf;
}

label {
    color: #B2BAC8;
    font-size: 14px;
    display: block;
    padding-bottom: 4px;
}

button {
    background-color: #7878bd;
    margin-top: 10px;
    font-size: 14px;
    padding: 7px 12px;
    font-weight: 500;
    color: white;
}

button:hover {
    background-color: #5f5f9c;
}
#op{
    margin-top:50px;
    flex-direction:column;
    display:flex;
}

#back {
    font-size: 15px;
    text-decoration: none;
    color: #7878bd;

}
#back:hover{
    color:#401031;
}
</style>