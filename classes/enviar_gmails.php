
<?php
// Incluir los archivos necesarios de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


require 'C:\wamp64\www\Web\abm\PHPMailer-master\src\Exception.php';
require 'C:\wamp64\www\Web\abm\PHPMailer-master\src\PHPMailer.php';
require 'C:\wamp64\www\Web\abm\PHPMailer-master\src\SMTP.php';

function sendVerificationCode($userEmail, $code,) {
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'presma@tecnica29de6.edu.ar';                     //SMTP username
        $mail->Password   = 'rdnbcymcztkmukww';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    
        //Recipients
        $mail->setFrom('presma@tecnica29de6.edu.ar', 'PRESMA');
        $mail->addAddress($userEmail, 'Usuario');     //Add a recipient
    
        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Tu codigo de verificacion PRESMA';
        $mail->Body    = 'Tu codigo de verificacion es ' . $code;
        $mail->AltBody = 'Tu codigo de verificacion es ' . $code;
    
        $mail->send();
        echo 'Tu codigo de verificacion fue enviado al gmail que ingresas';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
function generateVerificationCode($length = 6) {
    return substr(str_shuffle('123456789'), 0, $length);
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
    $stmt = $pdo->prepare('SELECT * FROM verification_codes WHERE user_id = ? AND code = ? DESC LIMIT 1');
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
