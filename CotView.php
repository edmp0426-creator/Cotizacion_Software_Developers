<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

// Procesar guardado de cotización
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_cotizacion'])) {
    $servername = "db";
    $username_db = "apti";
    $password_db = "apti";
    $dbname = "AdministracionProyectosTecnologiasInformacion";

    $conn = new mysqli($servername, $username_db, $password_db, $dbname);
    
    if ($conn->connect_error) {
        $mensaje = '<div style="color: red; background: #ffe6e6; padding: 10px; margin: 10px 0; border-radius: 4px;">Error de conexión: ' . $conn->connect_error . '</div>';
    } else {
        $conn->set_charset("utf8");
        
        $nombre = trim($_POST['nombre_cotizacion'] ?? 'Sin nombre');
        $total = floatval($_POST['total_cotizacion'] ?? 0);
        $username = $_SESSION['username'];

        // Obtener ID del usuario
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user_row = $result->fetch_assoc();
            $usuario_id = $user_row['id'];

            // Construir datos JSON
            $datos = [];
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'item_') === 0) {
                    $id = substr($key, 5);
                    $cantidad = floatval($value);
                    if ($cantidad > 0 && isset($_POST['label_' . $id]) && isset($_POST['costo_' . $id])) {
                        $datos[$id] = [
                            'label' => $_POST['label_' . $id],
                            'cantidad' => $cantidad,
                            'costo' => floatval($_POST['costo_' . $id])
                        ];
                    }
                }
            }
            
            $datos_json = json_encode($datos);

            // Guardar cotización
            $stmt = $conn->prepare("INSERT INTO historial_cotizaciones (usuario_id, nombre_cotizacion, datos_cotizacion, total_costo) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("issd", $usuario_id, $nombre, $datos_json, $total);

            if ($stmt->execute()) {
                $mensaje = '<div style="color: green; background: #e6ffe6; padding: 10px; margin: 10px 0; border-radius: 4px;">✓ Cotización guardada correctamente</div>';
            } else {
                $mensaje = '<div style="color: red; background: #ffe6e6; padding: 10px; margin: 10px 0; border-radius: 4px;">Error al guardar: ' . $stmt->error . '</div>';
            }
            $stmt->close();
        }
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Axotimate - Cotizador de software</title>
<link rel="icon" href="imagenes/logo.png" type="image/png">
<style>
    :root {
        --bg: #f8fafc;
        --surface: #ffffff;
        --surface-alt: #eef2ff;
        --text: #0f172a;
        --text-muted: #475569;
        --primary: #2563eb;
        --primary-dark: #1d4ed8;
        --accent: #14b8a6;
        --border: rgba(15, 23, 42, 0.08);
        --shadow: 0 24px 60px rgba(15, 23, 42, 0.1);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html {
        scroll-behavior: smooth;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: radial-gradient(circle at top, rgba(37, 99, 235, 0.16), transparent 35%), var(--bg);
        color: var(--text);
        min-height: 100vh;
    }

    a {
        color: inherit;
        text-decoration: none;
    }

    .page-header {
        width: 100%;
        background: rgba(255, 255, 255, 0.96);
        border-bottom: 1px solid var(--border);
        position: sticky;
        top: 0;
        z-index: 100;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
    }

    .page-header .header-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .brand-label {
        display: flex;
        flex-direction: row;
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
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--primary);
        letter-spacing: 0.06em;
    }

    .brand-subtitle {
        font-size: 0.95rem;
        color: var(--text-muted);
    }

    .header-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
    }

    .button,
    button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: none;
        border-radius: 999px;
        padding: 0.95rem 1.4rem;
        font-weight: 700;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        cursor: pointer;
    }

    .button:hover,
    button:hover {
        transform: translateY(-1px);
    }

    .button-primary {
        background: var(--primary);
        color: #fff;
        box-shadow: 0 14px 30px rgba(37, 99, 235, 0.18);
    }

    .button-secondary {
        background: rgba(37, 99, 235, 0.08);
        color: var(--primary);
    }

    .button-ghost {
        background: transparent;
        color: var(--text);
        border: 1px solid rgba(15, 23, 42, 0.12);
    }

    .page-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 30px 24px 48px;
    }

    .hero-panel {
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.95), rgba(56, 189, 248, 0.95));
        color: #fff;
        border-radius: 28px;
        padding: 40px 38px;
        display: grid;
        gap: 18px;
        box-shadow: var(--shadow);
        margin-bottom: 34px;
    }

    .hero-panel .eyebrow {
        text-transform: uppercase;
        letter-spacing: 0.24em;
        font-size: 0.78rem;
        opacity: 0.9;
    }

    .hero-panel h1 {
        font-size: clamp(2.5rem, 4vw, 3.8rem);
        line-height: 0.95;
        max-width: 760px;
    }

    .hero-panel p {
        max-width: 720px;
        font-size: 1.05rem;
        color: rgba(255, 255, 255, 0.92);
        margin-top: 6px;
    }

    .hero-cta-group {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 8px;
    }

    .hero-cta {
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .hero-cta:hover {
        background: rgba(255, 255, 255, 0.18);
    }

    .section-grid {
        display: grid;
        gap: 24px;
        margin-bottom: 28px;
    }

    .categoria-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 24px;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
        padding: 28px 28px 26px;
    }

    .categoria-titulo {
        margin: 0 0 8px;
        color: var(--primary);
        font-size: 1rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .categoria-subtitulo {
        margin: 0 0 22px;
        color: var(--text-muted);
        font-size: 0.95rem;
    }

    .rol-principal {
        align-items: center;
        display: flex;
        gap: 14px;
        margin: 0 0 18px;
    }

    .rol-principal input[type="checkbox"] {
        accent-color: var(--primary);
        width: 18px;
        height: 18px;
        flex: 0 0 auto;
    }

    .rol-principal label {
        color: var(--text);
        font-size: 1rem;
        font-weight: 700;
    }

    .categoria-card > br {
        display: none;
    }

    .contenedor {
        display: none;
        margin-left: 20px;
        padding-left: 16px;
        border-left: 2px dashed rgba(37, 99, 235, 0.18);
    }

    .contenedor > div {
        display: grid;
        grid-template-columns: auto 1fr auto;
        align-items: center;
        gap: 16px;
        padding: 16px 0;
        border-bottom: 1px solid rgba(15, 23, 42, 0.06);
    }

    .contenedor > div:last-child {
        border-bottom: none;
    }

    .contenedor label {
        font-weight: 600;
        color: var(--text);
    }

    .contenedor input[type="number"] {
        width: 90px;
        padding: 12px 14px;
        border: 1px solid rgba(15, 23, 42, 0.14);
        border-radius: 14px;
        background: #f8fafc;
        color: var(--text);
    }

    .contenedor p {
        margin: 0;
        color: var(--text-muted);
        font-size: 0.95rem;
    }

    .redesign-section {
        display: grid;
        gap: 24px;
        margin-top: 20px;
    }

    .action-buttons {
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .btn-modern {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 14px 24px;
        min-width: 170px;
        border-radius: 16px;
        border: none;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 20px 38px rgba(15, 23, 42, 0.08);
    }

    .btn-modern:hover {
        transform: translateY(-2px);
    }

    .btn-modern.btn-secondary {
        background: rgba(15, 23, 42, 0.08);
        color: var(--text);
    }

    #btnTotal {
        background: var(--accent);
        color: #fff;
    }

    .btn-history {
        background: var(--primary);
        color: #fff;
    }

    .grand-total-card {
        background: #0f172a;
        color: #fff;
        border-radius: 24px;
        padding: 28px;
        text-align: center;
        box-shadow: 0 28px 60px rgba(15, 23, 42, 0.14);
    }

    .grand-total-card span {
        display: block;
        margin-top: 12px;
        font-size: 3rem;
        font-weight: 800;
        letter-spacing: -0.03em;
    }

    .summary-card,
    .save-form-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 24px;
        padding: 28px;
        box-shadow: var(--shadow);
    }

    .summary-card h3,
    .save-form-card h3 {
        margin: 0 0 18px;
        color: var(--primary);
        font-size: 1.2rem;
    }

    .summary-card ul {
        list-style: none;
        display: grid;
        gap: 12px;
    }

    .summary-card li {
        background: #f8fafc;
        padding: 14px 18px;
        border-radius: 18px;
        border: 1px solid rgba(37, 99, 235, 0.12);
        color: var(--text);
    }

    .save-form-card label {
        display: block;
        margin-bottom: 10px;
        color: var(--text-muted);
        font-weight: 600;
    }

    .save-form-card input[type="text"] {
        width: 100%;
        padding: 14px 18px;
        border: 1px solid rgba(15, 23, 42, 0.14);
        border-radius: 18px;
        background: #f8fafc;
        color: var(--text);
        font-size: 1rem;
    }

    .form-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        margin-top: 20px;
        justify-content: space-between;
    }

    .form-actions .btn-modern {
        min-width: 170px;
        width: auto;
        flex: 1;
    }

    .form-actions .btn-modern:not(:last-child) {
        margin-right: 0;
    }

    @media (max-width: 768px) {
        .form-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .form-actions .btn-modern {
            width: 100%;
        }
    }

    @media (min-width: 900px) {
        .section-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .redesign-section {
            grid-template-columns: 1.4fr 0.9fr;
            align-items: start;
        }
    }

    @media (max-width: 768px) {
        .page-header .header-content {
            flex-direction: column;
            align-items: flex-start;
        }

        .hero-panel {
            border-radius: 24px;
            padding: 32px;
        }

        .section-grid {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column;
            align-items: stretch;
        }
    }

    @media (max-width: 520px) {
        .page-container {
            padding: 24px 16px 42px;
        }

        .contenedor > div {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .contenedor input[type="number"] {
            width: 100%;
        }

        .header-actions {
            width: 100%;
            justify-content: space-between;
        }

        .hero-cta-group {
            flex-direction: column;
            align-items: stretch;
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
                <a href="#" class="button button-secondary">¿Necesitas un CRM?</a>
                <a href="logout.php" class="button button-ghost">Cerrar sesión</a>
            </div>
        </div>
    </header>

    <div class="page-container">
        <section class="hero-panel">
            <span class="eyebrow">Crea tu cotización gratis</span>
            <h1>Convierte tu estimación en una propuesta profesional</h1>
            <p>Ingresa el detalle de tu equipo, ajusta cargos y guarda tu cotización en un diseño más moderno sin perder la esencia original.</p>
            <div class="hero-cta-group">
                <a href="historial.php" class="button button-secondary hero-cta">Ver cotizaciones guardadas</a>
            </div>
        </section>

        <div class="section-grid">
            <section class="categoria-card">
                <h2 class="categoria-titulo">Desarrollo</h2>
                <p class="categoria-subtitulo">Core del equipo</p>

    <!-- ================= BACKEND ================= -->
    <div class="rol-principal">
        <input type="checkbox" id="principalDB">
        <label for="principalDB">Desarrollador Backend</label>
    </div>

    <div id="contenedorDB" class="contenedor">

        <div>
            <input type="checkbox" class="opcionDB" data-id="1">
            <label>DB Junior ($20K)</label>
            <input type="number" id="db1" disabled>
            <p>Total: $<span id="resDB1">0</span></p>
        </div>

        <div>
            <input type="checkbox" class="opcionDB" data-id="2">
            <label>DB Semi-Senior ($30K)</label>
            <input type="number" id="db2" disabled>
            <p>Total: $<span id="resDB2">0</span></p>
        </div>

        <div>
            <input type="checkbox" class="opcionDB" data-id="3">
            <label>DB Senior ($40K)</label>
            <input type="number" id="db3" disabled>
            <p>Total: $<span id="resDB3">0</span></p>
        </div>

    </div>

    <br><br>

    <!-- ================= FRONTEND ================= -->
    <div class="rol-principal">
        <input type="checkbox" id="principalFR">
        <label for="principalFR">Desarrollador Frontend</label>
    </div>

    <div id="contenedorFR" class="contenedor">

        <div>
            <input type="checkbox" class="opcionFR" data-id="4">
            <label>DF Junior ($23K)</label>
            <input type="number" id="fr4" disabled>
            <p>Total: $<span id="resFR4">0</span></p>
        </div>

        <div>
            <input type="checkbox" class="opcionFR" data-id="5">
            <label>DF Semi-Senior ($40K)</label>
            <input type="number" id="fr5" disabled>
            <p>Total: $<span id="resFR5">0</span></p>
        </div>

        <div>
            <input type="checkbox" class="opcionFR" data-id="6">
            <label>DF Senior ($45K)</label>
            <input type="number" id="fr6" disabled>
            <p>Total: $<span id="resFR6">0</span></p>
        </div>

    </div>

    <br><br>

    <!-- ================= FULLSTACK ================= -->
    <div class="rol-principal">
        <input type="checkbox" id="principalFS">
        <label for="principalFS">Desarrollador Fullstack</label>
    </div>

    <div id="contenedorFS" class="contenedor">

        <div>
            <input type="checkbox" class="opcionFS" data-id="7">
            <label>FS Junior ($25K)</label>
            <input type="number" id="fs7" disabled>
            <p>Total: $<span id="resFS7">0</span></p>
        </div>

        <div>
            <input type="checkbox" class="opcionFS" data-id="8">
            <label>FS Semi-Senior ($40K)</label>
            <input type="number" id="fs8" disabled>
            <p>Total: $<span id="resFS8">0</span></p>
        </div>

        <div>
            <input type="checkbox" class="opcionFS" data-id="9">
            <label>FS Senior ($45K)</label>
            <input type="number" id="fs9" disabled>
            <p>Total: $<span id="resFS9">0</span></p>
        </div>

    </div>

    <br><br>

    </section>

    <section class="categoria-card">
        <h2 class="categoria-titulo">Gesti&oacute;n y coordinaci&oacute;n</h2>

    
    <!-- ================= PROJECT MANAGER ================= -->
    <div class="rol-principal">
        <input type="checkbox" id="principalPM">
        <label for="principalPM">Project Manager</label>
    </div>

    <div id="contenedorPM" class="contenedor">

        <div>
            <input type="checkbox" class="opcionPM" data-id="10">
            <label>PM Junior ($25K)</label>
            <input type="number" id="pm10" disabled>
            <p>Total: $<span id="resPM10">0</span></p>
        </div>

        <div>
            <input type="checkbox" class="opcionPM" data-id="11">
            <label>PM Semi-Senior ($40K)</label>
            <input type="number" id="pm11" disabled>
            <p>Total: $<span id="resPM11">0</span></p>
        </div>

        <div>
            <input type="checkbox" class="opcionPM" data-id="12">
            <label>PM Senior ($70K)</label>
            <input type="number" id="pm12" disabled>
            <p>Total: $<span id="resPM12">0</span></p>
        </div>

    </div>

    <br><br>

    <!-- ================= PRODUCT OWNER ================= -->
    <div class="rol-principal">
        <input type="checkbox" id="principalPO">
        <label for="principalPO">Product Owner</label>
    </div>

    <div id="contenedorPO" class="contenedor">

        <div>
            <input type="checkbox" class="opcionPO" data-id="13">
            <label>PO Junior ($25K)</label>
            <input type="number" id="po13" disabled>
            <p>Total: $<span id="resPO13">0</span></p>
        </div>

        <div>
            <input type="checkbox" class="opcionPO" data-id="14">
            <label>PO Semi-Senior ($40K)</label>
            <input type="number" id="po14" disabled>
            <p>Total: $<span id="resPO14">0</span></p>
        </div>

        <div>
            <input type="checkbox" class="opcionPO" data-id="15">
            <label>PO Senior ($70K)</label>
            <input type="number" id="po15" disabled>
            <p>Total: $<span id="resPO15">0</span></p>
        </div>

    </div>

    <br><br>

    </section>

    <section class="categoria-card">
        <h2 class="categoria-titulo">Dise&ntilde;o</h2>
    <!-- ================= UX/UI DESIGNER ================= -->
    <div class="rol-principal">
        <input type="checkbox" id="principalDS">
        <label for="principalDS">UX/UI Designer</label>
    </div>

    <div id="contenedorDS" class="contenedor">

        <div>
            <input type="checkbox" class="opcionDS" data-id="16">
            <label>DS Junior ($20K)</label>
            <input type="number" id="ds16" disabled>
            <p>Total: $<span id="resDS16">0</span></p>
        </div>

        <div>
            <input type="checkbox" class="opcionDS" data-id="17">
            <label>DS Semi-Senior ($30K)</label>
            <input type="number" id="ds17" disabled>
            <p>Total: $<span id="resDS17">0</span></p>
        </div>

        <div>
            <input type="checkbox" class="opcionDS" data-id="18">
            <label>DS Senior ($50K)</label>
            <input type="number" id="ds18" disabled>
            <p>Total: $<span id="resDS18">0</span></p>
        </div>

    </div>

    <br><br>

    </section>

    <section class="categoria-card">
        <h2 class="categoria-titulo">Calidad</h2>
    <!-- ================= QA Tester (Manual / Automatización) ================= -->
    <div class="rol-principal">
        <input type="checkbox" id="principalQA">
        <label for="principalQA">QA Tester</label>
    </div>

    <div id="contenedorQA" class="contenedor">

        <div>
            <input type="checkbox" class="opcionQA" data-id="19">
            <label>QA Junior ($18K)</label>
            <input type="number" id="qa19" disabled>
            <p>Total: $<span id="resQA19">0</span></p>
        </div>

        <div>
            <input type="checkbox" class="opcionQA" data-id="20">
            <label>QA Semi-Senior ($28K)</label>
            <input type="number" id="qa20" disabled>
            <p>Total: $<span id="resQA20">0</span></p>
        </div>

        <div>
            <input type="checkbox" class="opcionQA" data-id="21">
            <label>QA Senior ($45K)</label>
            <input type="number" id="qa21" disabled>
            <p>Total: $<span id="resQA21">0</span></p>
        </div>

    </div>

    <br><br>

    </section>

    <section class="categoria-card">
        <h2 class="categoria-titulo">Infraestructura</h2>
<!-- ================= DEVOPS ================= -->
    <div class="rol-principal">
        <input type="checkbox" id="principalDV">
        <label for="principalDV">DevOps Engineer</label>
    </div>

    <div id="contenedorDV" class="contenedor">

        <div>
            <input type="checkbox" class="opcionDV" data-id="22">
            <label>DV Junior ($25K)</label>
            <input type="number" id="dv22" disabled>
            <p>Total: $<span id="resDV22">0</span></p>
        </div>

        <div>
            <input type="checkbox" class="opcionDV" data-id="23">
            <label>DV Semi-Senior ($35K)</label>
            <input type="number" id="dv23" disabled>
            <p>Total: $<span id="resDV23">0</span></p>
        </div>

        <div>
            <input type="checkbox" class="opcionDV" data-id="24">
            <label>DV Senior ($45K)</label>
            <input type="number" id="dv24" disabled>
            <p>Total: $<span id="resDV24">0</span></p>
        </div>

    </div>

    <br><br>

    </section>

    <section class="categoria-card">
        <h2 class="categoria-titulo">Liderazgo T&eacute;cnico</h2>
<!-- ================= TECH LEAD ================= -->
    <div class="rol-principal">
        <input type="checkbox" id="principalTL">
        <label for="principalTL">Tech Lead</label>
    </div>

    <div id="contenedorTL" class="contenedor">

        <div>
            <input type="checkbox" class="opcionTL" data-id="25">
            <label>TL Junior ($35K)</label>
            <input type="number" id="tl25" disabled>
            <p>Total: $<span id="resTL25">0</span></p>
        </div>

        <div>
            <input type="checkbox" class="opcionTL" data-id="26">
            <label>TL Semi-Senior ($50K)</label>
            <input type="number" id="tl26" disabled>
            <p>Total: $<span id="resTL26">0</span></p>
        </div>

        <div>
            <input type="checkbox" class="opcionTL" data-id="27">
            <label>TL Senior ($90K)</label>
            <input type="number" id="tl27" disabled>
            <p>Total: $<span id="resTL27">0</span></p>
        </div>

    </div>

    </section>
        </div>
    
    <div class="redesign-section">
        <!-- Action Buttons Card -->
        <div class="action-buttons">
            <button id="btnTotal" class="btn-modern">Generar Ticket</button>
            <a href="historial.php" class="btn-modern btn-history">Ver Historial</a>
        </div>
        
        <!-- Grand Total Card -->
        <div class="grand-total-card">
            Total General: $<span id="totalGeneral">0</span>
        </div>
        
        <!-- Summary Card -->
        <div id="ticket" class="summary-card">
            <h3>Resumen</h3>
            <ul id="listaTicket"></ul>
        </div>
        
        <!-- Save Quote Form Card -->
        <form method="POST" class="save-form-card">
            <h3>Guardar Cotización</h3>
            <label for="nombre_cot">Nombre de la cotización:</label>
            <input type="text" id="nombre_cot" name="nombre_cotizacion" placeholder="ej: Proyecto XYZ" required>
            
            <div id="itemsOcultos"></div>
            <input type="hidden" name="total_cotizacion" id="total_hidden" value="0">
            
            <div class="form-actions">
                <button type="submit" name="guardar_cotizacion" value="1" class="btn-modern">Guardar Cotización</button>
                <button type="button" onclick="imprimirTicket()" class="btn-modern btn-secondary">Descargar PDF</button>
                <button type="button" onclick="enviarCorreo()" class="btn-modern btn-secondary">Enviar por Email</button>
            </div>
        </form>
    </div>
</div>

    <script>

    // ================= CONFIGURACIÓN =================
    const sueldos = {
        1: 20000, // Backend Junior
        2: 30000, // Backend Semi-Senior
        3: 40000, // Backend Senior
        4: 23000, // Frontend Junior
        5: 40000, // Frontend Semi-Senior
        6: 45000, // Frontend Senior
        7: 25000, // Fullstack Junior
        8: 40000, // Fullstack Semi-Senior
        9: 45000, // Fullstack Senior
        10: 25000, // Project Manager Junior
        11: 40000, // Project Manager Semi-Senior
        12: 70000, // Project Manager Senior
        13: 25000, // Product Owner Junior
        14: 40000, // Product Owner Semi-Senior
        15: 70000, // Product Owner Senior
        16: 20000, // UX/UI Designer Junior
        17: 30000, // UX/UI Designer Semi-Senior
        18: 50000, // UX/UI Designer Senior
        19: 18000, // QA Tester Junior
        20: 28000, // QA Tester Semi-Senior
        21: 45000, // QA Tester Senior
        22: 25000, // DevOps Engineer Junior
        23: 35000, // DevOps Engineer Semi-Senior
        24: 45000,  // DevOps Engineer Senior
        25: 35000,  // Tech Lead Junior
        26: 50000,  // Tech Lead Semi-Senior
        27: 90000   // Tech Lead Senior

    };

    // ================= FUNCIÓN REUTILIZABLE =================
    function configurarModulo(principalId, contenedorId, claseOpcion, prefijo) {

        const principal = document.getElementById(principalId);
        const contenedor = document.getElementById(contenedorId);

        // Mostrar / ocultar contenedor
        principal.addEventListener("change", () => {
            contenedor.style.display = principal.checked ? "block" : "none";
        });

        // Manejar opciones internas
        const opciones = contenedor.querySelectorAll("." + claseOpcion);

        opciones.forEach(op => {
            const id = op.dataset.id;
            const input = document.getElementById(prefijo + id);
            const res = document.getElementById("res" + prefijo.toUpperCase() + id);

            // Activar input
            op.addEventListener("change", () => {
                input.disabled = !op.checked;
                if (!op.checked) {
                    input.value = "";
                    res.textContent = "0";
                }
            });

            // Calcular resultado
            input.addEventListener("input", () => {
                const valor = parseFloat(input.value) || 0;
                res.textContent = valor * sueldos[id];
            });
        });
    }

    // ================= INICIALIZACIÓN =================
    configurarModulo("principalDB", "contenedorDB", "opcionDB", "db"); // Backend
    configurarModulo("principalFR", "contenedorFR", "opcionFR", "fr"); // Frontend
    configurarModulo("principalFS", "contenedorFS", "opcionFS", "fs"); // Fullstack
    configurarModulo("principalPM", "contenedorPM", "opcionPM", "pm"); // Project Manager
    configurarModulo("principalPO", "contenedorPO", "opcionPO", "po"); // Product Owner
    configurarModulo("principalDS", "contenedorDS", "opcionDS", "ds"); // UX/UI Designer
    configurarModulo("principalQA", "contenedorQA", "opcionQA", "qa"); // QA Tester
    configurarModulo("principalDV", "contenedorDV", "opcionDV", "dv"); // DevOps
    configurarModulo("principalTL", "contenedorTL", "opcionTL", "tl"); // Liderazgo Tecnico

    // ================= BOTON TICKET =================

    document.getElementById("btnTotal").addEventListener("click", () => {

        let total = 0;
        const lista = document.getElementById("listaTicket");
        const itemsOcultos = document.getElementById("itemsOcultos");
        lista.innerHTML = ""; // limpiar ticket
        itemsOcultos.innerHTML = ""; // limpiar inputs ocultos

        // SOLO checkboxes internos (no los principales)
        document.querySelectorAll(".opcionDB:checked, .opcionFR:checked, .opcionFS:checked, .opcionPM:checked, .opcionPO:checked, .opcionDS:checked, .opcionQA:checked, .opcionDV:checked, .opcionTL:checked").forEach(cb => {

            const contenedor = cb.parentElement;

            const label = contenedor.querySelector("label").textContent;
            const input = contenedor.querySelector("input[type='number']");
            const resultado = contenedor.querySelector("span");
            const id = cb.dataset.id;

            const cantidad = parseFloat(input.value) || 0;
            const subtotal = parseFloat(resultado.textContent) || 0;
            const costo = sueldos[id];

            if (cantidad > 0) {
                total += subtotal;

                const li = document.createElement("li");
                li.textContent = `${label} | Cantidad: ${cantidad} | Subtotal: $${subtotal}`;
                lista.appendChild(li);

                // Agregar inputs ocultos para el formulario
                const inputItem = document.createElement("input");
                inputItem.type = "hidden";
                inputItem.name = "item_" + id;
                inputItem.value = cantidad;
                itemsOcultos.appendChild(inputItem);

                const inputLabel = document.createElement("input");
                inputLabel.type = "hidden";
                inputLabel.name = "label_" + id;
                inputLabel.value = label;
                itemsOcultos.appendChild(inputLabel);

                const inputCosto = document.createElement("input");
                inputCosto.type = "hidden";
                inputCosto.name = "costo_" + id;
                inputCosto.value = costo;
                itemsOcultos.appendChild(inputCosto);
            }

        });

        document.getElementById("totalGeneral").textContent = total;
        document.getElementById("total_hidden").value = total;
    });
    // ================= BOTON PDF =================

        function imprimirTicket() {
        const contenido = document.getElementById("ticket").innerHTML;

        const ventana = window.open('', '', 'width=800,height=600');

        ventana.document.write(`
            <html>
            <head>
                <title>Ticket</title>
                <style>
                    body { font-family: Arial; padding: 20px; }
                    h3 { margin-bottom: 10px; }
                    li { margin-bottom: 5px; }
                </style>
            </head>
            <body>
                ${contenido}
            </body>
            </html>
        `);

        ventana.document.close();
        ventana.print();
    }
    // ================= BOTON EMAIL  =================

    function enviarCorreo() {
    let texto = "Resumen de costos:\n\n";

    document.querySelectorAll("#listaTicket li").forEach(li => {
        texto += li.textContent + "\n";
    });

    const total = document.getElementById("totalGeneral").textContent;
    texto += `\nTotal: $${total}`;

    const asunto = "Ticket de costos";
    const body = encodeURIComponent(texto);

    window.location.href = `mailto:?subject=${asunto}&body=${body}`;
    }

    </script>

</body>
</html>
