<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Calculadora de Sueldos</title>
<style>
    body { font-family: Arial; }
    .contenedor { display: none; margin-left: 20px; }
</style>
</head>
<body>
    <p>Desarrollo (core del equipo)</p>

    <!-- ================= BACKEND ================= -->
    <input type="checkbox" id="principalDB">
    <label for="principalDB">Desarrollador Backend</label>

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
    <input type="checkbox" id="principalFR">
    <label for="principalFR">Desarrollador Frontend</label>

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
    <input type="checkbox" id="principalFS">
    <label for="principalFS">Desarrollador Fullstack</label>

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

    <p>Gestión y coordinación</p>

    
    <!-- ================= PROJECT MANAGER ================= -->
    <input type="checkbox" id="principalPM">
    <label for="principalPM">Project Manager</label>

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
    <input type="checkbox" id="principalPO">
    <label for="principalPO">Product Owner</label>

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

    <p>Diseño</p>
    <!-- ================= UX/UI DESIGNER ================= -->
    <input type="checkbox" id="principalDS">
    <label for="principalDS">UX/UI Designer</label>

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

    <p>Calidad</p>
    <!-- ================= QA Tester (Manual / Automatización) ================= -->
    <input type="checkbox" id="principalQA">
    <label for="principalQA">QA Tester</label>

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

    <p>Infrestructura</p>
<!-- ================= DEVOPS ================= -->
    <input type="checkbox" id="principalDV">
    <label for="principalDV">DevOps Engineer</label>

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

    <p>Liderazgo Tecnico</p>
<!-- ================= TECH LEAD ================= -->
    <input type="checkbox" id="principalTL">
    <label for="principalTL">Tech Lead</label>

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

    <br><br>
    <!-- ================= Generar Ticket ================= -->
    <br><br>
    <button id="btnTotal">Generar Ticket</button>

    <h2>Total General: $<span id="totalGeneral">0</span></h2>

    <div id="ticket" style="margin-top:20px; border:1px solid #ccc; padding:10px;">
        <h3>Resumen</h3>
        <ul id="listaTicket"></ul>
    </div>

    <!-- ================= Generar PDF ================= -->
    <button onclick="imprimirTicket()">Descargar PDF</button>

    <!-- ================= Generar email ================= -->
    <button onclick="enviarCorreo()">Enviar por Email</button>

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
        lista.innerHTML = ""; // limpiar ticket

        // SOLO checkboxes internos (no los principales)
        document.querySelectorAll(".opcionDB:checked, .opcionFR:checked, .opcionFS:checked, .opcionPM:checked, .opcionPO:checked, .opcionDS:checked, .opcionQA:checked, .opcionDV:checked, .opcionTL:checked").forEach(cb => {

            const contenedor = cb.parentElement;

            const label = contenedor.querySelector("label").textContent;
            const input = contenedor.querySelector("input[type='number']");
            const resultado = contenedor.querySelector("span");

            const cantidad = parseFloat(input.value) || 0;
            const subtotal = parseFloat(resultado.textContent) || 0;

            if (cantidad > 0) {
                total += subtotal;

                const li = document.createElement("li");
                li.textContent = `${label} | Cantidad: ${cantidad} | Subtotal: $${subtotal}`;
                lista.appendChild(li);
            }

        });

        document.getElementById("totalGeneral").textContent = total;
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