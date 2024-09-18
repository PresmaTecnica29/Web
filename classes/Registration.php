<?php

/**
 * Class registration
 * handles the user registration
 */
class Registration
{
    private $db_connection = null;
    public $errors = array();
    public $messages = array();

    public function __construct()
    {
        if (isset($_POST["register"])) {
            $this->registerNewUser();
        }
    }

    private function registerNewUser()
    {
        $dominiosValidos = array("@alu.tecnica29de6.edu.ar", "@tecnica29de6.edu.ar");
        if (empty($_POST['user_name'])) {
            $this->errors[] = "Usuario vacio";
        } elseif (empty($_POST['user_password_new']) || empty($_POST['user_password_repeat'])) {
            $this->errors[] = "Contraseña vacia";
        } elseif ($_POST['user_password_new'] !== $_POST['user_password_repeat']) {
            $this->errors[] = "Las contraseñas no son iguales";
        } elseif (strlen($_POST['user_password_new']) < 6) {
            $this->errors[] = "La contraseña debe tener como minimo 6 caracteres";
        } elseif (strlen($_POST['user_name']) > 64 || strlen($_POST['user_name']) < 2) {
            $this->errors[] = "El Nombre de Usuario no puede tener menos de 2 letras o mas de 64";
        } elseif (!preg_match('/^[a-z\d]{2,64}$/i', $_POST['user_name'])) {
            $this->errors[] = "El nombre de usuario solo puede contener letras y números, entre 2 y 64 caracteres";
        } elseif (empty($_POST['user_email'])) {
            $this->errors[] = "Correo electronico vacio";
        } elseif (strlen($_POST['user_email']) > 64) {
            $this->errors[] = "El correo no puede tener más de 64 caracteres";
        } elseif (!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "El formato de tu correo electronico no es valido";
        } elseif (!empty($_POST['user_name'])
            && strlen($_POST['user_name']) <= 64
            && strlen($_POST['user_name']) >= 2
            && preg_match('/^[a-z\d]{2,64}$/i', $_POST['user_name'])
            && !empty($_POST['user_email'])
            && strlen($_POST['user_email']) <= 64
            && filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)
            && (substr_compare($_POST['user_email'], "@alu.tecnica29de6.edu.ar", -strlen("@alu.tecnica29de6.edu.ar")) === 0 || substr_compare($_POST['user_email'], "@tecnica29de6.edu.ar", -strlen("@tecnica29de6.edu.ar")) === 0)
            && !empty($_POST['user_password_new'])
            && !empty($_POST['user_password_repeat'])
            && ($_POST['user_password_new'] === $_POST['user_password_repeat'])
        ) {
            // Aquí iría la conexión a la base de datos
            $this->messages[] = "Tu cuenta ya fue creada. Ya puedes iniciar sesion.";
        } else {
            $this->errors[] = "Ocurrió un error desconocido.";
        }
    }
}

// Instancia la clase de registro cuando la página se carga
$registration = new Registration();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>

    <!-- Aquí va el estilo embebido en el archivo PHP -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }

        .form-container h1 {
            text-align: center;
        }

        .field {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .submit-btn {
            width: 100%;
            padding: 10px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .submit-btn:hover {
            background-color: #4cae4c;
        }

        /* Estilos para los mensajes de error y éxito */
        .error-message, .success-message {
            padding: 15px;
            border-radius: 5px;
            margin: 20px auto;
            font-size: 16px;
            line-height: 1.5;
            text-align: left;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error-message p, .success-message p {
            margin: 0 0 10px 0;
        }

        .error-message:before {
            content: "⚠️ ";
            font-size: 20px;
            margin-right: 10px;
        }

        .success-message:before {
            content: "✅ ";
            font-size: 20px;
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h1>Registrarse</h1>

        <!-- Mensajes de error y éxito -->
        <?php if ($registration->errors) : ?>
            <div class="error-message">
                <?php foreach ($registration->errors as $error) : ?>
                    <p><?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($registration->messages) : ?>
            <div class="success-message">
                <?php foreach ($registration->messages as $message) : ?>
                    <p><?= $message ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Formulario de registro -->
        <form method="post" action="" style="width: 50%; justify-content: center; margin-left: 150px;">
            <div class="field">
                <label for="login_input_username">Usuario</label>
                <input type="text" name="user_name" id="login_input_username" placeholder="Usuario" required>
            </div>

            <div class="field">
                <label for="login_input_email">Email</label>
                <input type="email" name="user_email" id="login_input_email" placeholder="Example@alu.tecnica29de6.edu.ar" required>
            </div>

            <div class="field">
                <label for="login_input_password_new">Contraseña</label>
                <input type="password" name="user_password_new" id="login_input_password_new" placeholder="Contraseña (min 6 caracteres)" pattern=".{6,}" required>
            </div>

            <div class="field">
                <label for="login_input_password_repeat">Repetir Contraseña</label>
                <input type="password" name="user_password_repeat" id="login_input_password_repeat" placeholder="Repite tu contraseña" pattern=".{6,}" required>
            </div>

            <div class="field">
                <label for="rol">Rol</label>
                <select name="rol" id="rol">
                    <option value="0">Selecciona un rol</option>
                    <option value="1">Profesor</option>
                    <option value="2">Otro</option>
                </select>
            </div>

            <div class="field">
                <input type="submit" name="register" value="Registrarme" class="submit-btn">
            </div>
        </form>
    </div>
</body>

</html>