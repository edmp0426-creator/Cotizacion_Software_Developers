# Plan para hacer header pegado al tope 100% ancho sin márgenes

## Estado: Pendiente

- [ ] 1. Editar CoTCss.css: Agregar estilos .axotimate-heading pegado (sticky top0 width100% border-radius0), .header-content flex center, .logout-header-link, body.page-header { margin/padding:0 }
- [ ] 2. Editar CotView.php: Quitar padding body 32px0 ->0, mover <a Cerrar sesión> dentro de div.axotimate-heading (usando flex), remover estilos inline del header
- [ ] 3. Editar historial.php: Cambiar body margin20px ->0, agregar div.axotimate-heading arriba con logout dentro, agregar clase 'page-header' al body
- [ ] 4. Verificar en navegador (sugerir abrir CotView.php y historial.php)

**Siguiente paso:** Editar CoTCss.css
