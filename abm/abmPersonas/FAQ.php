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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preguntas Frecuentes (FAQ)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .faq-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .faq-item {
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }

        .faq-question {
            width: 100%;
            background-color: #0d6efd;
            color: #fff;
            padding: 15px;
            border: none;
            text-align: left;
            font-size: 18px;
            cursor: pointer;
            outline: none;
            transition: background-color 0.3s ease;
        }

        .faq-question:hover {
            background-color: #0056b3;
        }

        .faq-answer {
            display: none;
            padding: 15px;
            background-color: #f9f9f9;
        }
    </style>
</head>

<body>
    <div class="faq-container">
        <h1>Preguntas Frecuentes (FAQ)</h1>

        <div class="faq-item">
            <button class="faq-question" style='color: #e4e4e4'>¿Un usuario puede sacar varios equipos?</button>
            <div class="faq-answer">
                <p>El prestamo de computadoras esta limitado a 1 por usuario, ningun usuario puede tener mas de un prestamo activo al mismo tiempo. Si se quiere usar otra computadora diferente de la ya retirada el usuario debera devolver la computadora que posee y pedir otro prestamo.</p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" style='color: #e4e4e4'>¿Se puede extender el lapso del préstamo ?</button>
            <div class="faq-answer">
                <p>Si, se pueden extender los lapsos de prestamo desde la pagina de registros</p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" style='color: #e4e4e4'>¿Como poner una computadora en mantenimiento?</button>
            <div class="faq-answer">
                <p>Las computadoras pueden presentar una falla en su sistema que imposibilite su uso o alguno de sus componentes puede dejar de funcionar. Una computadora en estas condiciones no puede ser usada hasta ser restaurada. </p>
                    
                <p>Para deshabilitar una computadora y ponerla en estado de Mantenimiento dirigase a la pagina "Recursos" y busque la computadora que desea deshabilitar. Una vez la encuentre, haga click en el boton "Poner en Mantenimiento" ubicado en el lado derecho de la pantalla.
                Tenga en cuenta que una computadora en Mantenimiento no puede ser prestada a ningun usuario. </p>
                    
                <p>Para habilitar nuevamente una computadora y dejarla lista para ser prestada tendra que buscar la misma computadora y clickear en el boton "Habilitar" que se encuentra a un costado del visto previamente, esto lo que hara sera cambiar el estado de la computadora seleccionada a "Libre".
                </p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question" style='color: #e4e4e4  '>¿Qué pasa si se vence el lapso del préstamo?</button>
            <div class="faq-answer">
                <p>Como dijo Maradona:     Eeeeeeeeeeeeeeeeeeeeeeehhhhhhhhh...</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question" style='color: #e4e4e4  '>¿Que sucede cuando un usuario es bloqueado?</button>
            <div class="faq-answer">
                <p>Cuando un usuario es bloqueado por un administrador, se inhabilita su capacidad para realizar prestamos a traves de la aplicacion movil, al igual que su capacidad para devolver materiales.</p>
                <p>Esta sancion se puede aplicar por diversos motivos a usuarios que hayan cometido algun tipo de accion inadecuada y quedara a juicio del administrador correspondiente el aplicar esta penalizacion o deshacerla.</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question" style='color: #e4e4e4  '>¿Pregunta 6?</button>
            <div class="faq-answer">
                <p>Respuesta a la pregunta 6.</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question" style='color: #e4e4e4  '>¿Pregunta 7?</button>
            <div class="faq-answer">
                <p>Respuesta a la pregunta 7.</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question" style='color: #e4e4e4  '>¿Que hago si ninguna de estas era mi duda?</button>
            <div class="faq-answer">
                <p>En caso de que no hayas encontrado la respuesta a tu pregunta, puedes contactar a los desarrolladores de esta web via mail, envia un correo electronico expresando tu inquietud y seras respondido lo mas pronto posible! Recuerda ser paciente, no eres el unico que nos plantea sus dudas.</p>
                <p>Contacto: presmaatencionalclienteoficial@gmail.com
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const questions = document.querySelectorAll('.faq-question');

            questions.forEach(question => {
                question.addEventListener('click', () => {
                    const answer = question.nextElementSibling;

                    if (answer.style.display === 'block') {
                        answer.style.display = 'none';
                    } else {
                        answer.style.display = 'block';
                    }
                });
            });
        });
    </script>
</body>
</html>

<?php include "../template/footer.php"; ?>