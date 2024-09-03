<?php
include '../funciones.php';
csrf();
if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  die();
}
include "../template/header.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>
<?php
$config = include('../../config/db.php');
$conexion = conexion();

$error = false;
    $nombre = $_GET['nombre'];
    $stmt = $conexion -> prepare("UPDATE `recurso` SET `recurso_estado` = '3' WHERE `recurso_nombre` = ?");
    $stmt->execute([$nombre]);  
?>

<div style='background-color:white'>
  <p style='margin-top: 25px; margin-left: 25px;'>Netbook puesta en mantenimiento exitosamente!</p> 
  <a href="./qr.php" class="btn btn-primary mt-4" style='margin-left: 25px; margin-top:1px;'>Volver Atras</a>
  <?php include "../template/footer.php"; ?>
</div>