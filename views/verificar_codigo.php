<?php
require "../classes/enviar_gmails.php";
require "../config/db.php";

$conexion = conexion();

if (isset($_POST['login_input_email']) && isset($_POST['registerinput_verfcode'])) {
    $user_email = $_POST['user_email'];
    $codeinput = $_POST['registerinput_verfcode'];

    $sql = "SELECT user_id FROM users WHERE user_email = :user_email";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':user_email', $user_email, PDO::PARAM_STR);
    $stmt->execute();

    $fetchid = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($fetchid) {
        $userid = $fetchid["user_id"];
        if (verifyCode($userid, $codeinput)) {
            echo "Código verificado correctamente.";
        } else {
            echo "Código de verificación incorrecto.";
        }
    } else {
        echo "Usuario no encontrado.";
    }
} else {
    echo "Faltan datos en la solicitud.";
}
?>