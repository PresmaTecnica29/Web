
<?php
// Incluir los archivos necesarios de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'C:\wamp64\www\Web\abm\PHPMailer-master\src\Exception.php';
require 'C:\wamp64\www\Web\abm\PHPMailer-master\src\PHPMailer.php';
require 'C:\wamp64\www\Web\abm\PHPMailer-master\src\SMTP.php';

function sendVerificationCode($userEmail, $code) {
    $mail = new PHPMailer(true);
    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Cambia esto al servidor SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'presma@tecnica29de6.edu.ar'; // Tu dirección de correo
        $mail->Password = 'Presma1234!'; // Tu contraseña
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Remitente y destinatario
        $mail->setFrom('presma@tecnica29de6.edu.ar', 'Presma');
        $mail->addAddress($userEmail);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Código de Verificación para la pagina PRESMA';
        $mail->Body = 'Tu código de verificación es: ' . $code;

        $mail->send();
        echo 'Correo de verificación enviado.';
    } catch (Exception $e) {
        echo 'El correo no pudo ser enviado. Mailer Error: ', $mail->ErrorInfo;
    }
}
function generateVerificationCode($length = 6) {
    return substr(str_shuffle('0123456789'), 0, $length);
}

function storeVerificationCode($userId, $code) {
    // Conexión a la base de datos
    $pdo = new PDO('mysql:host=localhost;dbname=login', 'root', '');
    $stmt = $pdo->prepare('INSERT INTO verification_codes (user_id, code) VALUES (?, ?)');
    $stmt->execute([$userId, $code]);
}

function verifyCode($userId, $code) {
    // Conexión a la base de datos
    $pdo = new PDO('mysql:host=localhost;dbname=login', 'root', '');
    $stmt = $pdo->prepare('SELECT * FROM verification_codes WHERE user_id = ? AND code = ? ORDER BY created_at DESC LIMIT 1');
    $stmt->execute([$userId, $code]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // El código es válido
        return true;
    } else {
        // El código no es válido
        return false;
    }
}




// Ejemplo de uso
//$userEmail = 'user@example.com'; // El correo del usuario
//$userId = 1; // ID del usuario (debería ser obtenido después del registro o login)
//$code = generateVerificationCode();
//storeVerificationCode($userId, $code);
//sendVerificationCode($userEmail, $code);

// Ejemplo de uso
//$enteredCode = '123456'; // Código ingresado por el usuario
//if (verifyCode($userId, $enteredCode)) {
 //   echo 'Código verificado correctamente.';
//} else {
//    echo 'Código incorrecto.';
//}
//function deleteVerificationCode($userId, $code) {
    // Conexión a la base de datos
   // $pdo = new PDO('mysql:host=localhost;dbname=login', 'root', '');
 //   $stmt = $pdo->prepare('DELETE FROM verification_codes WHERE user_id = ? AND code = ?');
 //   $stmt->execute([$userId, $code]);
//}

// Ejemplo de uso
//if (verifyCode($userId, $enteredCode)) {
//    deleteVerificationCode($userId, $enteredCode);
//}

 /* write new user's data into database
                    $sql = "INSERT INTO users (`user_name`, `user_password_hash`, `user_email`,`idRol` ) VALUES ('$user_name','$user_password_hash','$user_email','$user_rol')";
                    $query_new_user_insert = $this->db_connection->query($sql);

                    // if user has been added successfully
                    if ($query_new_user_insert) {
                        $this->messages[] = "Tu cuenta ya fue creada. Ya puedes iniciar sesion.";
                    } else {
                        $this->errors[] = "Perdon, tu registracion fallo. Porfavor intentalo devuelta.";
                    }
                }*/


?>
