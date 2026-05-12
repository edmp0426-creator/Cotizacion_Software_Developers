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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Axotimate - Detalles de cotización</title>
    <link rel="icon" href="imagenes/logo.png" type="image/png">
    <style>
        :root {
            --bg: #f8fafc;
            --surface: #ffffff;
            --text: #0f172a;
            --text-muted: #475569;
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --accent: #14b8a6;
            --border: rgba(15, 23, 42, 0.08);
            --shadow: 0 24px 60px rgba(15, 23, 42, 0.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: radial-gradient(circle at top, rgba(37, 99, 235, 0.16), transparent 35%), var(--bg); color: var(--text); min-height: 100vh; }
        .page-header { width: 100%; background: rgba(255, 255, 255, 0.96); border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 100; box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08); }
        .page-header .header-content { max-width: 1200px; margin: 0 auto; padding: 24px; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
        .brand-label { display: flex; align-items: center; gap: 18px; }
        .brand-logo { width: 100px; height: 100px; display: block; }
        .brand-label > div { display: flex; flex-direction: column; gap: 3px; }
        .brand-title { color: var(--primary); font-size: 1.6rem; font-weight: 800; letter-spacing: 0.06em; }
        .brand-subtitle { color: var(--text-muted); font-size: 0.95rem; }
        .header-actions { display: flex; align-items: center; justify-content: flex-end; gap: 10px; flex-wrap: wrap; }
        .app-button { display: inline-flex; align-items: center; justify-content: center; border: none; border-radius: 999px; padding: 0.95rem 1.4rem; font-weight: 700; text-decoration: none; transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease; cursor: pointer; }
        .app-button:hover { transform: translateY(-1px); }
        .app-button-secondary { background: rgba(37, 99, 235, 0.08); color: var(--primary); }
        .app-button-ghost { background: transparent; color: var(--text); border: 1px solid rgba(15, 23, 42, 0.12); }
        .contenedor { max-width: 1000px; margin: 30px auto; background-color: var(--surface); padding: 28px; border-radius: 24px; box-shadow: var(--shadow); border: 1px solid var(--border); display: flex; flex-direction: column; gap: 20px; }
        .detail-header { display: flex; align-items: center; gap: 20px; padding-bottom: 20px; border-bottom: 2px solid var(--border); }
        .detail-logo { width: 160px; height: 160px; display: block; }
        .detail-header h1 { color: var(--primary); border: none; padding: 0; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid var(--border); padding: 14px; text-align: left; }
        th { background-color: var(--accent); color: white; font-weight: bold; }
        td { color: var(--text); }
        .total-final { text-align: right; margin-top: 20px; font-size: 18px; font-weight: bold; color: var(--primary); }
        .btn-volver { display: inline-block; align-self: flex-start; background-color: var(--primary); color: white; padding: 8px 16px; text-decoration: none; border-radius: 999px; font-size: 0.9rem; font-weight: 600; transition: all 0.2s ease; box-shadow: 0 8px 18px rgba(37, 99, 235, 0.16); }
        .btn-volver:hover { transform: translateY(-2px); background-color: var(--primary-dark); }
        @media (max-width: 768px) { .page-header .header-content { align-items: flex-start; flex-direction: column; } }
        @media (max-width: 520px) { .header-actions { width: 100%; justify-content: space-between; } }
    </style>
</head>
<body>
<header class="page-header">
    <div class="header-content">
        <div class="brand-label">
            <img src="imagenes/logo.png?v=2" alt="Axotimate Logo" class="brand-logo">
            <div>
                <span class="brand-title">Axotimate</span>
                <span class="brand-subtitle">Cotiza proyectos de software con claridad y confianza</span>
            </div>
        </div>
        <div class="header-actions">
            <a href="historial.php" class="app-button app-button-secondary">Historial</a>
            <a href="logout.php" class="app-button app-button-ghost">Cerrar sesión</a>
        </div>
    </div>
</header>
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

