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
<title>Calculadora de Sueldos</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f7f7f7;
        color: #0f2140;
        margin: 0;
        padding: 32px 0;
    }

    .contenedor { display: none; margin-left: 20px; }

    .categoria-card {
        background: #fff;
        border: 1px solid #e9e9e9;
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(15, 33, 64, 0.14);
        box-sizing: border-box;
        margin: 0 auto 24px;
        max-width: 672px;
        padding: 34px 32px 28px;
    }

    .categoria-titulo {
        color: #0758ff;
        font-size: 20px;
        font-weight: 700;
        margin: 0 0 8px;
    }

    .categoria-subtitulo {
        color: #536079;
        font-size: 13px;
        margin: 0 0 26px;
    }

    .rol-principal {
        align-items: center;
        display: flex;
        gap: 12px;
        margin: 0 0 20px;
    }

    .rol-principal input[type="checkbox"] {
        accent-color: #424242;
        flex: 0 0 auto;
        height: 20px;
        width: 20px;
    }

    .rol-principal label {
        color: #0f2140;
        font-size: 16px;
        font-weight: 700;
    }

    .categoria-card > br {
        display: none;
    }
    
    /* Simplified CotView Redesign Styles */
    .redesign-section {
        max-width: 700px;
        margin: 30px auto;
        padding: 0 16px;
    }
    
    .action-buttons {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }
    
    .btn-modern {
        padding: 12px 24px;
        font-size: 15px;
        font-weight: 500;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    #btnTotal.btn-modern {
        background: #2563eb;
        color: white;
    }
    
    #btnTotal.btn-modern:hover {
        background: #1d4ed8;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }
    
    .btn-history {
        background: #0ea5e9;
        color: white;
    }
    
    .btn-history:hover {
        background: #0284c7;
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
    }
    
    .btn-modern.btn-secondary {
        background: #6b7280;
        color: white;
    }
    
    .btn-modern.btn-secondary:hover {
        background: #4b5563;
        box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
    }
    
    .grand-total-card {
        background: #2563eb;
        color: white;
        text-align: center;
        padding: 24px;
        border-radius: 12px;
        margin-bottom: 24px;
        box-shadow: 0 4px 16px rgba(37, 99, 235, 0.2);
        font-size: 24px;
        font-weight: 600;
    }
    
    .grand-total-card span {
        font-size: 1.8em;
    }
    
    .summary-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
    }
    
    .summary-card h3 {
        color: #2563eb;
        margin-bottom: 16px;
        font-size: 20px;
    }
    
    .summary-card ul {
        list-style: none;
    }
    
    .summary-card li {
        background: #f9fafb;
        padding: 12px;
        margin-bottom: 8px;
        border-radius: 8px;
        border-left: 3px solid #2563eb;
        font-size: 15px;
    }
    
    .save-form-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
    }
    
    .save-form-card h3 {
        color: #2563eb;
        margin-bottom: 16px;
        font-size: 20px;
    }
    
    .save-form-card label {
        display: block;
        margin-bottom: 6px;
        color: #374151;
        font-weight: 500;
    }
    
    .save-form-card input[type="text"] {
        width: 100%;
        padding: 12px;
        border: 2px solid #d1d5db;
        border-radius: 8px;
        font-size: 15px;
        transition: border-color 0.2s ease;
        margin-bottom: 16px;
    }
    
    .save-form-card input[type="text"]:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.05);
    }
    
    .form-actions {
        display: flex;
        gap: 10px;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .redesign-section {
            padding: 0 16px;
        }
        
        .action-buttons {
            flex-direction: column;
            align-items: center;
        }
        
        .btn-modern {
            width: 100%;
            max-width: 300px;
        }
        
        .grand-total-card {
            padding: 24px 20px;
            font-size: 24px;
        }
        
        .grand-total-card span {
            font-size: 1.8em;
        }
        
        .summary-card, .save-form-card {
            padding: 24px 20px;
        }
    }
    
    @media (max-width: 480px) {
        .grand-total-card {
            font-size: 20px;
        }
        
        .grand-total-card span {
            font-size: 1.5em;
        }
    }
    
</style>
</head>
<body>
    <a href="logout.php" style="float: right;">Cerrar sesión</a>
    <?php echo $mensaje; ?>
    
    <!-- Axotimate Full-Width Header using project colors -->
    <div class="axotimate-heading" style="
        background: linear-gradient(135deg, #2563eb 0%, #0758ff 100%);
        color: white;
        text-align: center;
        padding: 32px 20px;
        border-radius: 0 0 20px 20px;
        box-shadow: 0 8px 32px rgba(37, 99, 235, 0.3);
        margin: 0 0 40px 0;
        width: 100vw;
        position: relative;
        left: 50%;
        right: 50%;
        margin-left: -50vw;
        margin-right: -50vw;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-weight: 700;
    ">
        <h1 style="
            font-size: 36px;
            margin: 0 0 8px 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        ">Axotimate</h1>
        <p style="
            font-size: 16px;
            opacity: 0.95;
            margin: 0;
        ">Software Development Cost Estimator</p>
    </div>
    <section class="categoria-card">
        <h2 class="categoria-titulo">Desarrollo</h2>
        <p class="categoria-subtitulo">core del equipo</p>

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
