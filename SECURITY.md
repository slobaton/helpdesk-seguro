# Helpdesk Seguro

## Alcance
Se construyó una aplicación web de soporte que maneja **tickets con adjuntos privados**.  
La aplicación tiene roles de **Administrador** y **Técnico**, un tablero **Kanban** (solo visible para estos roles) y una vista de **“Mis tickets”** para los solicitantes.

---

## Principales riesgos identificados

- **IDOR (Insecure Direct Object Reference):** un usuario podría intentar acceder a tickets o adjuntos de otros manipulando IDs en las URLs.
- **Subida de archivos maliciosos:** intento de subir archivos camuflados (ej. PHP renombrado o imagen polyglot).
- **XSS (almacenado o reflejado):** inyección de código en campos como título, descripción o resolución.
- **CSRF:** riesgo en formularios sensibles (cambio de estado, asignación, subida de adjuntos).
- **Overposting / Mass Assignment:** envío de campos indebidos (ej. `resolved_by`) para alterar la lógica interna.
- **Clickjacking / MIME sniffing / fuga de referer:** si no se fijan cabeceras adecuadas, la app podría quedar expuesta.
- **Autorización deficiente en Kanban:** un Técnico podría mover tickets sin estar asignado o dejar movimientos inconsistentes.
- **Fuerza bruta / abuso de endpoints:** automatización de cambios de estado o subida de adjuntos → posible DoS lógico.
- **Fuga de adjuntos:** riesgo si los archivos usan rutas públicas o enlaces estáticos.
- **Dependencias y configuración:** librerías vulnerables, claves filtradas o cookies mal configuradas.

---

## Control de acceso y autorización

Se implementaron **Policies de Laravel** para tickets y adjuntos:

- **Ver un ticket:** Admin, creador o técnico asignado.
- **Actualizar / cambiar estado / cerrar:** Admin o técnico asignado.
- **Asignar:**
    - Admin → sin límites.
    - Técnico → solo auto-asignarse si el ticket está libre.

En el **Kanban**, cuando un Técnico mueve un ticket sin asignación:
1. El sistema lo auto-asigna.
2. Luego valida la autorización.

---

## Validación y entradas

- Reglas centralizadas en **FormRequests**:
    - Uso de *enums* válidos, límites de longitud, control de tamaño y arrays acotados.
- Para prevenir **mass assignment**:
    - Uso exclusivo de `validated()`.
    - Limitación de `$fillable`.
    - Campos críticos (ej. `resolved_by`) no aceptados desde el cliente.

---

## Subida y descarga de archivos

- Adjuntos guardados en **almacenamiento privado**, no expuestos con `storage:link`.
- Descargas solo vía **controlador** con *policy* y cabeceras:
    - `X-Content-Type-Options: nosniff`
- Validación del **MIME real** (no solo extensión).
- Solo tipos de imagen permitidos: **jpeg, png, webp**.
- Nombres de archivos **aleatorios** y rutas segregadas por **ticket** y **etapa** (problema | resolución).

---

## Protección del frontend

- Escape por defecto de **Blade (`{{ }}`)**, evitando `{!! !!}` con datos de usuario.
- **CSP básica** con:
    - `default-src 'self'`
    - sin *inline scripts* innecesarios.
- Middleware de Laravel asegura **tokens CSRF** en formularios.

---

## Cabeceras y endurecimiento

Cabeceras de seguridad implementadas:

- `X-Frame-Options: DENY`
- `X-Content-Type-Options: nosniff`
- `Referrer-Policy: strict-origin-when-cross-origin`
- CSP básica

---

## Registro y trazabilidad

Se usa **Activity Log (Spatie)** para registrar acciones clave:

- Creación
- Asignación
- Cambios de estado
- Adjuntos y descargas

Esto habilita una **auditoría de incidentes de seguridad**.

---

## Tasa y abuso

- **Rate limiting** en rutas sensibles (estado, adjuntos, asignación).
- **Paginación y límites** en consultas del tablero Kanban.
- Prevención de abusos o saturación (DoS lógico).

---

## Justificación de decisiones

- **Policies y Roles (Spatie):**  
  Patrón estándar en Laravel → aplica mínimo privilegio, separa permisos de la lógica, facilita auditoría.

- **Almacenamiento privado + descargas controladas:**  
  Se evita exponer archivos directamente. Cada acceso pasa por autorización y cabeceras seguras.

- **FormRequests:**  
  Centralizan validación y autorización, reduciendo errores y manteniendo reglas consistentes.

- **Livewire y Blade:**  
  Evitan exposición de APIs extra, aprovechando CSRF y *escape automático*.

- **CSP y cabeceras de seguridad:**  
  Refuerzan la aplicación contra XSS, clickjacking, sniffing → defensa en profundidad.

- **Auto-asignación en Kanban:**  
  Mantiene usabilidad para Técnicos, pero garantiza que solo el asignado pueda modificar estados.  
