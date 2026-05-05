<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

$servername = "db";
$username_db = "apti";
$password_db = "apti";
$dbname = "AdministracionProyectosTecnologiasInformacion";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");  // Full emoji support

$username = $_SESSION['username'];
$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    die("Invalid ID");
}

// Get user ID
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user_row = $result->fetch_assoc();
$usuario_id = $user_row['id'];
$stmt->close();

// Fetch quotation
$stmt = $conn->prepare("SELECT * FROM historial_cotizaciones WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $id, $usuario_id);
$stmt->execute();
$cotizacion = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$cotizacion) {
    die("Quotation not found or access denied");
}

$datos = json_decode($cotizacion['datos_cotizacion'], true);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles de Cotización</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .contenedor { max-width: 1000px; margin: 0 auto; background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .total-final { text-align: right; margin-top: 20px; font-size: 18px; font-weight: bold; color: #007bff; }
        .btn-volver { background-color: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
<div class="contenedor">
    <h1>Detalles de Cotización: <?php echo htmlspecialchars($cotizacion['nombre_cotizacion']); ?></h1>
    <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($cotizacion['fecha_creacion'])); ?></p>
    
    <table>
        <thead>
            <tr>
                <th>Posición</th>
                <th>Cantidad</th>
                <th>Costo Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($datos as $item): ?>
                <?php if ($item['cantidad'] > 0): ?>
                    <?php 
                    $subtotal = $item['cantidad'] * $item['costo'];
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['label']); ?></td>
                        <td><?php echo $item['cantidad']; ?></td>
                        <td>$<?php echo number_format($item['costo'], 0); ?></td>
                        <td>$<?php echo number_format($subtotal, 0); ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="total-final">Total: $<?php echo number_format($cotizacion['total_costo'], 2); ?></div>
    
    <a href="historial.php" class="btn-volver">← Volver al Historial</a>
</div>
<?php $conn->close(); ?>
</body>
</html>

