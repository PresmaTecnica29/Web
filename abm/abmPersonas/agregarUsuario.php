<?php

include '../funciones.php';

// Comprobación si hash_equals existe o no
if (!function_exists('hash_equals')) {
    function hash_equals($str1, $str2) {
        if (strlen($str1) != strlen($str2)) {
            return false;
        } else {
            $res = $str1 ^ $str2;
            $ret = 0;
            for ($i = strlen($res) - 1; $i >= 0; $i--) {
                $ret |= ord($res[$i]);
            }
            return !$ret;
        }
    }
}

// Verificación del token CSRF
csrf();
if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  die();
}
if (isset($_POST['submit'])) {
  $resultado = [
    'error' => false,
    'mensaje' => 'El usuario ' . escapar($_POST['nombre']) . ' ha sido agregado con éxito'
  ];

  $config = include '../../config/db.php';

  try {
    $conexion = conexion();

    $password_encriptada = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $usuario = [
      "user_name"   => $_POST['nombre'],
      "user_email" => $_POST['email'],
      "user_password_hash" => $password_encriptada,
      "idRol"    => $_POST['rol']
    ];

    $consultaSQL = "INSERT INTO users (user_name, user_email, user_password_hash, idRol)";
    $consultaSQL .= "values (:" . implode(", :", array_keys($usuario)) . ")";

    $sentencia = $conexion->prepare($consultaSQL);
    $sentencia->execute($usuario);

    $ultimoIdInsertado = $conexion->lastInsertId();

    $consultaSQL = "INSERT INTO user_security (user_id, must_change_password) VALUES (:user_id, TRUE)";
    $sentencia = $conexion->prepare($consultaSQL);
    $sentencia->execute(['user_id' => $ultimoIdInsertado]);
  } catch (PDOException $error) {
    $resultado['error'] = true;
    $resultado['mensaje'] = $error->getMessage();
  }
}

require_once("../../config/db.php");
$conexion = conexion();
$statement = $conexion->prepare("SELECT * FROM rol");
$statement->execute();
$datos = $statement->fetchAll();
?>

<?php include "../template/header.php"; ?>

<?php
if (isset($resultado)) {
?>
  <div class="container mt-3">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-<?= $resultado['error'] ? 'danger' : 'success' ?>" role="alert">
          <?= $resultado['mensaje'] ?>
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
      <h2 class="mt-4">Crear un usuario</h2>
      <hr>
      <form method="post">
        <div class="form-group">
          <label for="nombre">Nombre</label>
          <input type="text" name="nombre" id="nombre" class="form-control">
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="text" name="email" id="email" class="form-control">
        </div>
        <br>
        <div class="form-group">
          <label for="password">Contraseña</label>
          <input type="password" name="password" id="password" class="form-control">
        </div>
        <br>
        <div class="form-group">
          <label for="rol">Rol</label>
          <select name="rol" id="rol" class="input">
            <?php
            // Obtener el rol del usuario desde la sesión
            $user_rol = isset($_SESSION['user_rol']) ? $_SESSION['user_rol'] : 5;

            foreach ($datos as $dato) {
                // Mostrar todas las opciones si el rol del usuario es 5
                if ($user_rol == 5) {
                    echo '<option value="' . $dato['idRol'] . '" class="input"> ' . $dato['rol_descripcion'] . ' </option>';
                } else {
                    // Filtrar opciones según el rol del usuario
                    if ($dato['idRol'] < $user_rol) {
                        echo '<option value="' . $dato['idRol'] . '" class="input"> ' . $dato['rol_descripcion'] . ' </option>';
                    }
                }
            }
            ?>
          </select>
        </div>
        <br>
        <br>
        <div class="form-group">
          <input name="csrf" type="hidden" value="<?php echo escapar($_SESSION['csrf']); ?>">
          <a class="btn btn-primary" href="abmPersonas.php" style='background-color: red'>Volver Atras</a>
          <input type="submit" name="submit" class="btn btn-primary" value="Aceptar" style='background-color: green'>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include "../template/footer.php"; ?>