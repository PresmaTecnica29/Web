<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información del Usuario</title>
    <link rel="stylesheet" type="text/css" href="../netbook/style.css">
</head>

<body>
<?php
include '../funciones.php';

csrf();
if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
    die();
}

$error = false;
$config = include('../../config/db.php');

// Conectar a la base de datos
$conexion = conexion();

try {
// Obtener el ID del usuario
$userId = $_GET['id'];

} catch (PDOException $error) {
    $resultado['error'] = true;
    $resultado['mensaje'] = $error->getMessage();
  }

// Consulta para obtener la información del usuario
$stmt = $conexion->prepare("
    SELECT u.user_name, u.user_email, u.bloqueado, u.user_area, u.idRol, r.rol_descripcion, a.area_nombre
    FROM users u
    LEFT JOIN rol r ON u.idRol = r.idRol
    LEFT JOIN area a ON u.user_area = a.id
    WHERE u.user_id = ?
");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Consulta para obtener el historial de préstamos
$historialStmt = $conexion->prepare("
    SELECT r.*, u.user_name AS prestado_por
    FROM registros r
    LEFT JOIN users u ON r.idusuario = u.user_id
    WHERE r.idusuario = ?
    ORDER BY r.inicio_prestamo DESC
");
$historialStmt->execute([$userId]);
$historial = $historialStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include "../template/header.php"; ?>

<a href="abmPersonas.php" class="boton" style="float: right; margin-right: 20px; margin-top: 20px;">Volver Atras</a>


<div class="user-info">
    <h1>Información del Usuario</h1>
    <p><strong>Nombre de Usuario:</strong> <?php echo htmlspecialchars($user['user_name']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['user_email']); ?></p>
    <p><strong>Usuario Bloqueado:</strong> <?php echo $user['bloqueado'] ? 'Sí' : 'No'; ?></p>
    <p><strong>Rol:</strong> <?php echo htmlspecialchars($user['rol_descripcion']); ?></p>
    <p><strong>Área Asignada:</strong> <?php echo htmlspecialchars($user['area_nombre']); ?></p>
</div>


<!-- Sección de Historial de Préstamos -->
<div class="historial-prestamos">
    <h2>Historial de Préstamos</h2>
    <ul>
        <?php foreach ($historial as $registro): ?>
            <li>
                <strong>Recurso:</strong> <?php echo htmlspecialchars($registro['idrecurso']); ?>,
                <strong>Prestado por:</strong> <?php echo htmlspecialchars($registro['prestado_por']); ?>,
                <strong>Fecha de Préstamo:</strong> <?php echo htmlspecialchars($registro['inicio_prestamo']); ?>,
                <strong>Fecha de Devolución:</strong> <?php echo htmlspecialchars($registro['fin_prestamo_fecha']); ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php if (empty($historial)): ?>
        <p>No hay registros de préstamos para este usuario.</p>
    <?php endif; ?>
</div>

<?php include "../template/footer.php"; ?>
</body>
</html>
