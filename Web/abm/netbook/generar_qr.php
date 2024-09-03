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

// Incluye la biblioteca
include('phpqrcode/qrlib.php'); 

$idnet = $_GET['id'];
    $nombre = $_GET['nombre'];
    // Genera un identificador único para el nombre del archivo
    $filePath = 'qrcodes/qrcode_' . $nombre . '.png';
    QRcode::png($idnet, $filePath);
?>
<div style='background-color:white'>
  <p style='margin-top: 25px; margin-left: 25px;'>Código QR generado exitosamente! <img src="<?php echo $filePath; ?>" alt="" height="5000px" width="5000px" style='margin-left:10px;'></p> 
  <a href="./qr.php" class="btn btn-primary mt-4" style='margin-left: 25px; margin-top:1px;'>Volver Atras</a>
  <?php include "../template/footer.php"; ?>
</div>