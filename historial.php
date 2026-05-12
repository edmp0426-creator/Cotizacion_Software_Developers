<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

// Configuración de la base de datos
$servername = "db";
$username_db = "apti";
$password_db = "apti";
$dbname = "AdministracionProyectosTecnologiasInformacion";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Obtener ID del usuario
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user_row = $result->fetch_assoc();
$usuario_id = $user_row['id'];
$stmt->close();

// Obtener historial de cotizaciones del usuario
$stmt = $conn->prepare("SELECT id, nombre_cotizacion, total_costo, fecha_creacion, datos_cotizacion FROM historial_cotizaciones WHERE usuario_id = ? ORDER BY fecha_creacion DESC");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$cotizaciones = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Axotimate - Historial de cotizaciones</title>
    <link rel="icon" href="imagenes/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* Modern Historial Design - Matches CotView */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: radial-gradient(circle at top, rgba(37, 99, 235, 0.16), transparent 35%), #f8fafc;
            min-height: 100vh;
            color: #0f172a;
        }
        .contenedor {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .page-header {
            width: 100%;
            background: rgba(255, 255, 255, 0.96);
            border-bottom: 1px solid rgba(15, 23, 42, 0.08);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        }
        .page-header .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .brand-label {
            display: flex;
            align-items: center;
            gap: 18px;
        }
        .brand-logo {
            width: 100px;
            height: 100px;
            display: block;
        }
        .brand-label > div {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }
        .brand-title {
            color: #2563eb;
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: 0.06em;
        }
        .brand-subtitle {
            color: #475569;
            font-size: 0.95rem;
        }
        .header-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
        }
        .app-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 999px;
            padding: 0.95rem 1.4rem;
            font-weight: 700;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
            cursor: pointer;
        }
        .app-button:hover {
            transform: translateY(-1px);
        }
        .app-button-secondary {
            background: rgba(37, 99, 235, 0.08);
            color: #2563eb;
        }
        .app-button-ghost {
            background: transparent;
            color: #0f172a;
            border: 1px solid rgba(15, 23, 42, 0.12);
        }
        .page-title {
            color: #1e293b;
            font-size: 34px;
            text-align: center;
            margin: 14px 0 8px;
        }
        .page-subtitle {
            color: #475569;
            font-size: 17px;
            text-align: center;
            margin: 0 0 28px;
        }
        .encabezado {
            display: none;
        }
        .historial-actions > a {
            display: none;
        }
        .historial-actions button {
            margin-left: 0 !important;
        }
        .botones-superior {
            margin-bottom: 20px;
        }
        .botones-superior a, .botones-superior button {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
            font-size: 14px;
        }
        .botones-superior a:hover, .botones-superior button:hover {
            background-color: #0056b3;
        }
        .logout-btn {
            background-color: #ffffff !important;
            float: right;
        }
        .logout-btn:hover {
            background-color: #ffffff !important;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th {
            background-color: #007bff;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        table tr:hover {
            background-color: #f9f9f9;
        }
        .acciones {
            text-align: center;
        }
        .btn-ver {
            background-color: #28a745;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            border: none;
        }
        .btn-ver:hover {
            background-color: #218838;
        }
        .btn-eliminar {
            background-color: #dc3545;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            border: none;
            margin-left: 5px;
        }
        .btn-eliminar:hover {
            background-color: #c82333;
        }
        .sin-datos {
            text-align: center;
            padding: 40px;
            color: #999;
            font-size: 16px;
        }
        @media (max-width: 768px) {
            .page-header .header-content {
                align-items: flex-start;
                flex-direction: column;
            }
        }
        @media (max-width: 520px) {
            .header-actions {
                width: 100%;
                justify-content: space-between;
            }
        }
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
            <a href="CotView.php" class="app-button app-button-secondary">Nueva Cotización</a>
            <a href="logout.php" class="app-button app-button-ghost">Cerrar sesión</a>
        </div>
    </div>
</header>

<div class="contenedor">
    <h1 class="page-title">Historial de Cotizaciones</h1>
    <p class="page-subtitle">Tus cotizaciones de desarrollo software</p>
    <!-- Axotimate Header -->
    <div class="encabezado">
        <div class="encabezado-content">
            <h1 style="color: white; font-size: 42px; font-weight: bold; margin: 0; font-family: 'Segoe UI', Tahoma, sans-serif;">Axotimate - Historial</h1>
            <p style="opacity: 0.95; margin: 5px 0 0 0; font-size: 18px;">Tus cotizaciones de desarrollo software</p>
        </div>
        <a href="logout.php" style="position: absolute; top: 20px; right: 20px; color: rgba(255,255,255,0.9); text-decoration: none; font-weight: 500; background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 20px;">Cerrar sesión</a>
    </div>

    <div class="historial-actions" style="text-align: center; margin-bottom: 30px;">
        <a href="CotView.php" style="background: #10b981; color: white; padding: 14px 28px; text-decoration: none; border-radius: 12px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(16,185,129,0.3); transition: all 0.3s;">← Nueva Cotización</a>
        <button onclick="limpiarHistorial()" style="background: #f59e0b; color: white; padding: 14px 28px; border: none; border-radius: 12px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(245,158,11,0.3); cursor: pointer; transition: all 0.3s; margin-left: 15px;">Limpiar Historial</button>
    </div>

    <h2 style="color: #1e293b; font-size: 28px; text-align: center; margin-bottom: 20px;">Usuario: <strong style="color: #2563eb;"><?php echo htmlspecialchars($_SESSION['username']); ?></strong></h2>

    <?php if ($cotizaciones->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Nombre Cotización</th>
                    <th>Total</th>
                    <th>Fecha</th>
                    <th class="acciones">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $cotizaciones->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nombre_cotizacion']); ?></td>
                        <td><strong>$<?php echo number_format($row['total_costo'], 2); ?></strong></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($row['fecha_creacion'])); ?></td>
                        <td class="acciones">
                            <a href="detail.php?id=<?php echo (int) $row['id']; ?>" class="btn-ver" title="Ver Detalles"><i class="fas fa-eye"></i> Ver Detalles</a>
                            <button type="button" class="btn-eliminar" onclick="eliminarCotizacion(<?php echo (int) $row['id']; ?>)">Eliminar</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="sin-datos">
            <p>No hay cotizaciones guardadas aún.</p>
            <a href="CotView.php" style="color: #007bff; text-decoration: underline;">Crear una nueva cotización</a>
        </div>
    <?php endif; ?>

</div>

<script>
function eliminarCotizacion(id) {
    if (confirm('¿Estás seguro que deseas eliminar esta cotización?')) {
        fetch('eliminar_cotizacion.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Cotización eliminada');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

function limpiarHistorial() {
    if (confirm('¿Estás seguro que deseas eliminar TODO el historial? Esta acción no se puede deshacer.')) {
        fetch('limpiar_historial.php', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Historial limpiado');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}
</script>

</body>
</html>

<?php
$conn->close();
?>
