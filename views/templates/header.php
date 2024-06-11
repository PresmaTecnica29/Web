<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <title>Presma</title>
    <link rel="icon" href="views/templates/logofinal.png" type="image/png">
    <link rel="stylesheet" href="views/templates/stylelog.css">
</head>

<body>
    <header class="p-3 bg-dark text-white">
        <div class="container" bis_skin_checked="1">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start" bis_skin_checked="1">
                <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                    <li><a href="/public/index.php" class="nav-link px-2 text-secondary">Inicio</a></li>
                    
                    <li><a href='abm/netbook/abm.php' class="nav-link px-2 text-white">Registros</a></li>
                    <?php
                    if (isset($_SESSION['user_rol'])) {
                        if ($_SESSION['user_rol'] == 5) {
                            echo '<li><a href="abm/abmPersonas/abmPersonas.php" class="nav-link px-2 text-white">Usuarios</a></li>';
                            echo '<li><a href="abm/netbook/qr.php" class="nav-link px-2 text-white">Recursos</a></li>';
                        }
                    } ?>
                    <li><a href="/public/index.php?logout" class="nav-link px-2 text-white">Cerrar Sesion</a></li>
                </ul>

                <div class="text-end" bis_skin_checked="1">
                    <button class="btn btn-warning" id="openMenuBtn"><?php echo $_SESSION['user_name']; ?></button>
                    <script>
                        document.getElementById("openMenuBtn").addEventListener("click", function() {
                            var menu = document.getElementById("menu");
                            menu.classList.toggle("active");
                        });
                    </script>
                    <img src="views/templates/logofinal.png">
                </div>
            </div>
        </div>
    </header>
    <div class="cuerpo">
        <a href="abm/netbook/abm.php" class="card">
            Registros
        </a>
        <a href="abm/netbook/visual.php" class="card">
            Visual
        </a>
        <?php
        if (isset($_SESSION['user_rol'])) {
            if ($_SESSION['user_rol'] == 5) {
                echo '<a href="abm/netbook/qr.php" class="card">Creacion Qr</a>';
            }
        } ?>
    </div>
    <footer class="bg-light text-center text-lg-start">
        <!-- Copyright -->
        <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
            © 2024 Presma
            <a href = "https://www.tecnica29de6.edu.ar/" class="text-dark">Escuela Técnica N° 29 DE 6 Reconquista de Buenos Aires</a>
        </div>
        <!-- Copyright -->
    </footer>
    <nav id="menu">
    </nav>

</body>
</html>