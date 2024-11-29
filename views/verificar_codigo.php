<?php
require "../classes/enviar_gmails.php";

// Depuración: Ver el contenido de $_POST
echo "<pre>";
print_r($_POST);  // Muestra todo el arreglo $_POST
echo "</pre>";

if (isset($_POST['user_email']) && isset($_POST['verfcode_input'])) {
    // Depuración: Mostrar el valor de $_POST['user_email']
    echo "Valor de 'user_email': " . $_POST['user_email'] . "<br>";

    // Sanitizar y obtener los datos
    $user_email = ($_POST['user_email']);
    $verifCode = ($_POST['verfcode_input']);

    // Verificar si el email es válido
    if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        echo "El formato del email no es válido.<br>";
        echo "Email: $user_email";
        exit;
    }

    // Obtener el ID del usuario
    $user_id = getUserId($user_email);

    if ($user_id) {
        // Verificar el código
        $resultverf = verifyCode($user_id, $verifCode);

        if ($resultverf === true) {  // Asegurarse de hacer una comparación correcta
            // Activar el usuario si el código es correcto
            if (activateUser($user_id)) {
                echo "El usuario ha sido activado correctamente.<br>";
                echo "User ID: $user_id<br>";
                echo "Email: $user_email<br>";
            } else {
                echo "Hubo un problema al activar el usuario.";
            }
        } else {
            echo "El código de verificación no es válido.";
        }
    } else {
        echo "No se encontró un usuario con ese email.";
    }

} else {
    // Verificar qué datos faltan en la solicitud
    if (!isset($_POST['user_email'])) {
        echo "El campo 'user_email' no está en la solicitud.";
    }
    if (!isset($_POST['verfcode_input'])) {
        echo "El campo 'verfcode_input' no está en la solicitud.";
    }
}
