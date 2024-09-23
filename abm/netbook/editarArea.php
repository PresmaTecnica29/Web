<?php
include '../funciones.php';

// Función para comparación segura
function safe_equals($a, $b) {
    return strlen($a) === strlen($b) && hash('sha256', $a) === hash('sha256', $b);
}

// Verificar CSRF
csrf();
if (isset($_POST['submit']) && !safe_equals($_SESSION['csrf'], $_POST['csrf'])) {
    die();
}

$config = include '../../config/db.php';
$conexion = conexion();
$resultado = [
    'error' => false,
    'mensaje' => ''
];

// Comprobar si el ID del área está presente
if (!isset($_GET['id'])) {
    $resultado['error'] = true;
    $resultado['mensaje'] = 'El área no existe o el ID es inválido';
}

if (isset($_POST['submit'])) {
    try {
        $conexion = conexion();

        // Actualizar los datos del área
        $area = [
            "id"        => $_GET['id'],
            "nombre"    => $_POST['area_nombre']
        ];

        $consultaSQL = "UPDATE area SET area_nombre = :nombre WHERE id = :id";
        $consulta = $conexion->prepare($consultaSQL);
        $consulta->execute($area);

        $resultado['mensaje'] = 'El área ha sido actualizada correctamente';
    } catch (PDOException $error) {
        $resultado['error'] = true;
        $resultado['mensaje'] = $error->getMessage();
    }
}

try {
    // Cargar el área seleccionada
    $id = $_GET['id'];
    $consultaSQL = "SELECT * FROM area WHERE id = :id";
    $sentencia = $conexion->prepare($consultaSQL);
    $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
    $sentencia->execute();
    $area = $sentencia->fetch(PDO::FETCH_ASSOC);

    if (!$area) {
        $resultado['error'] = true;
        $resultado['mensaje'] = 'No se ha encontrado el área';
    }
} catch (PDOException $error) {
    $resultado['error'] = true;
    $resultado['mensaje'] = $error->getMessage();
}
?>

<?php include "../template/header.php"; ?>

<?php
if ($resultado['error']) {
?>
    <div class="container mt-2">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-danger" role="alert">
                    <?= $resultado['mensaje'] ?>
                </div>
            </div>
        </div>
    </div>
<?php
}
?>

<?php
if (isset($_POST['submit']) && !$resultado['error']) {
?>
    <div class="container mt-2">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-success" role="alert">
                    <?= $resultado['mensaje'] ?>
                </div>
            </div>
        </div>
    </div>
<?php
}
?>

<?php
if (isset($area) && $area) {
?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="mt-4">Editando el área <?= escapar($area['area_nombre']) ?></h2>
                <hr>
                <form method="post">
                    <div class="form-group">
                        <label for="area_nombre">Nombre del Área</label>
                        <input type="text" name="area_nombre" id="area_nombre" value="<?= escapar($area['area_nombre']) ?>" class="form-control" required>
                    </div>
                    <br>
                    <div class="form-group">
                        <input name="csrf" type="hidden" value="<?php echo escapar($_SESSION['csrf']); ?>">
                        <input type="submit" name="submit" class="btn btn-primary" value="Actualizar">
                        <a class="btn btn-primary" href="areas.php" style="margin-left: 10px;">Regresar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>

<?php include "../template/footer.php"; ?>