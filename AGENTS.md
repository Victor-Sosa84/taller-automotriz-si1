# JECOES Tronic — Sistema de Gestión de Taller Automotriz

## Stack
- PHP 8.2, Laravel 11, MySQL, Blade, CSS tema oscuro personalizado
- Deploy: Railway + MySQL (https://taller-automotriz-si1-production.up.railway.app)
- Repo: https://github.com/Victor-Sosa84/taller-automotriz-si1.git

## Equipo
- Pither Daniel Condori Villanueva (Reg. 224027085)
- Victor David Sosa Coca (Reg. 223044431)
- Ambos participan en documentación UML, diagramas, diseño de BD e implementación

## Actores
- **Recepcionista**: usuario principal de los módulos de clientes, vehículos, ingresos, proformas y órdenes de trabajo
- **Mecánico / Técnico**: registra diagnósticos, solicita herramientas, ejecuta trabajos y registra repuestos y mano de obra
- **Administrador (Jefe de Taller)**: acceso total a todos los módulos, gestiona personal, permisos y parámetros globales

## Casos de uso
| ID | Nombre | Ciclo | Prioridad |
|---|---|---|---|
| CU-01 | Gestionar cliente | 1 | Alta |
| CU-02 | Gestionar ficha técnica de vehículo | 1 | Media |
| CU-03 | Consultar historial de mantenimiento | 1 | Media |
| CU-04 | Gestionar ingreso de unidad | 2 | Media |
| CU-05 | Realizar diagnóstico técnico | 2 | Media |
| CU-06 | Elaborar proforma | 2 | Alta |
| CU-07 | Emitir cotización | 2 | Alta |
| CU-08 | Gestionar estado de proforma | 2 | Alta |
| CU-09 | Gestionar préstamo de herramientas | 3 | Baja |
| CU-10 | Gestionar estado de herramientas | 3 | Baja |
| CU-11 | Gestionar contratos de trabajo | 4 | Baja |
| CU-12 | Liquidar pagos de personal | 4 | Baja |
| CU-13 | Gestionar usuarios y permisos | 1 | Alta |
| CU-14 | Gestionar orden de trabajo | 3 | Alta |
| CU-15 | Asignar responsables a tareas | 3 | Media |
| CU-16 | Registrar repuestos y mano de obra ejecutada | 3 | Media |
| CU-17 | Generar factura final | 4 | Alta |
| CU-18 | Registrar pago y cuotas | 4 | Media |
| CU-19 | Iniciar sesión | 1 | Alta |
| CU-20 | Cerrar sesión | 1 | Alta |
| CU-21 | Consultar bitácora | 1 | Baja |
| CU-22 | Generar reportes por comando de voz | 4 | Alta |

## Paquetes
| Paquete | CU incluidos |
|---|---|
| P1 Gestión de Recepción | CU-01, CU-02, CU-03, CU-04, CU-05 |
| P2 Gestión Comercial y Facturación | CU-06, CU-07, CU-08, CU-17, CU-18 |
| P3 Gestión Administrativa | CU-11, CU-12, CU-13, CU-19, CU-20, CU-21 |
| P4 Gestión Operativa | CU-09, CU-10, CU-14, CU-15, CU-16, CU-22 |

## Diagrama de clases — entidades principales
| Entidad | Atributos clave |
|---|---|
| `persona` | ci (PK), nombre, telefono, direccion, nit, es_cliente, es_personal |
| `usuario` | id_usuario (PK), id_rol (FK), ci_personal (FK), nombre_usuario, clave, correo |
| `rol` | id (PK), nombre, descripcion |
| `permiso` | id (PK), nombre, etiqueta, caso_uso, paquete |
| `rol_permiso` | id_permiso (FK), id_rol (FK) — PK compuesta, estado, fecha_registro, observaciones |
| `tipo_trabajador` | id (PK), descripcion |
| `adquiere` | ci_personal (FK), id_tipo_trabajador (FK) — PK compuesta |
| `tipo_remuneracion` | nro (PK), descripcion |
| `contrato` | id (PK), ci_personal (FK), tipo_remuneracion (FK), fecha_inicio, fecha_fin, estado, periodo_pago, valor |
| `pago` | id (PK), id_contrato (FK), fecha_pago, monto, tipo, metodo |
| `tipo_herramienta` | id (PK), descripcion |
| `marca_herramienta` | id (PK), nombre |
| `herramienta` | nro (PK), id_tipo_herramienta (FK), id_marca_herramienta (FK), descripcion, estado, disponible |
| `prestamo_herramienta` | id (PK), fecha_salida, fecha_devolucion (nullable) |
| `detalle_prestamo` | id_prestamo_herramienta (FK), nro_herramienta (FK) — PK compuesta, estado_salida, estado_retorno |
| `auto` | placa (PK), marca, modelo, anio, color, tipo |
| `diagnostico` | id (PK), fecha, ci_personal (FK), placa_auto (FK), descripcion |
| `detalle_diagnostico` | id_diagnostico (FK), id_detalle_diagnostico — PK compuesta, **falla** (texto de la falla detectada; antes se llamaba `descripcion`, renombrada) |
| `mano_obra` | id (PK), descripcion, costo_referencial |
| `proforma` | nro (PK), ci_cliente (FK), id_diagnostico (FK), fecha, total_aprox, estado, plazo |
| `repuesto` | id (PK), nombre, estado, marca, precio_referencial |
| `proforma_repuesto` | nro_proforma (FK), id_repuesto (FK) — PK compuesta, cantidad, precio_unitario, descuento |
| `proforma_servicio` | nro_proforma (FK), id_mano_obra (FK) — PK compuesta, costo, estado, cantidad |
| `orden_trabajo` | nro (PK), nro_proforma (FK, **nullable** — la OT nace sin proforma vinculada, recién se asigna al aprobarla), placa_auto (FK, nullable), fecha_inicio, fecha_fin, estado (Pendiente de Diagnóstico → Diagnóstico Finalizado → En Proceso → Finalizada / Anulada), kilometraje, observacion_entrada, observacion_salida |
| `detalle_repuesto` | nro_orden_trabajo (FK), id_repuesto (FK) — PK compuesta, cantidad, precio_unitario, descuento |
| `detalle_trabajo` | nro_orden_trabajo (FK), id_mano_obra (FK) — PK compuesta, costo, estado, cantidad |
| `realiza` | ci_personal (FK), nro_orden_trabajo (FK), id_mano_obra (FK) — PK compuesta, tipo_participacion, **pagado** (boolean, default false — evita doble pago en `PagoController::calcularPago()`, ver más abajo) |
| `recoge` | nro_orden_trabajo (FK), ci_persona (FK) — PK compuesta, relacion, fecha. ⚠️ **Existe en el esquema pero ningún CU la llena todavía** (ver Pendientes conocidos) |
| `factura` | nro (PK), nro_orden_trabajo (FK), fecha_emision, nit (string, **NOT NULL**), nombre, total, plazo |
| `detalle_factura` | nro_factura (FK), id — **PK compuesta** (`nro_factura`, `id`), descripcion, tipo, cantidad, precio, precio_unitario, descuento |
| `cuota` | nro_factura (FK), **nro** (no `nro_cuota`) — PK compuesta (`nro_factura`, `nro`), monto, fecha, tipo_pago (`efectivo`/`tarjeta`), referencia_stripe |
| `bitacora` | id (PK), id_usuario (FK), fecha_hora, accion, ip_equipo |

## Arquitectura clave
- RBAC dinámico: permisos formato `CU04_ADD`, middleware `CheckPermiso`
- Usuario `id=1` (aprincipal) bypassa todo sin consultar BD
- Sin `$timestamps` en la mayoría de modelos
- Tablas sin prefijo, PKs personalizadas (`nro`, `ci`, `placa`)
- Permisos: Paquete → Caso de Uso → Permiso granular (ADD/MOD/ELI/BUS)
- ⚠️ El MySQL local no tiene `STRICT_TRANS_TABLES` activado: un `NULL` insertado en una columna `NOT NULL` (ej. `factura.nit`) no genera error, se guarda como cadena vacía `''` silenciosamente. Verificar manualmente los seeders en columnas obligatorias, no confiar en que MySQL avise.

## Ciclos completados
- **Ciclo 1:** CU-01 a CU-03, CU-13, CU-19, CU-20, CU-21
- **Ciclo 2:** CU-04, CU-05, CU-06, CU-07, CU-08 + PDF de cotización con dompdf
- **Ciclo 3:** CU-09, CU-10, CU-14, CU-15, CU-16 + Catálogo Taller
- **Ciclo 4:** CU-11, CU-12, CU-17, CU-18, CU-22 + PDF/Excel de reportes, Stripe, Gemini

## Flujo principal implementado
Ingresos (CU-04) → Diagnóstico (CU-05) → Proforma (CU-06) → Emitir/Estado (CU-07/08) → OT (CU-14) → Asignación (CU-15) → Detalles (CU-16) → Factura (CU-17) → Pago/Cuotas (CU-18)

Transversal: Reportes por comando de voz (CU-22), consulta cualquier punto del flujo según el permiso del usuario

## Modelos existentes
`Usuario`, `Persona`, `Cliente`, `Personal`, `Auto`, `OrdenTrabajo`, `Diagnostico`, `DetalleDiagnostico`, `Proforma`, `ProformaRepuesto`, `ProformaServicio`, `Repuesto`, `ManoObra`, `DetalleRepuesto`, `DetalleTrabajo`, `Bitacora`,
`Realiza`, `PrestamoHerramienta`, `DetallePrestamo`, `Herramienta`, `TipoHerramienta`, `MarcaHerramienta`,
`Contrato`, `Pago`, `TipoRemuneracion`, `Factura`, `DetalleFactura`, `Cuota`

## Estados de proforma
`Borrador` → `Emitida` → `Aprobada` / `Observada` / `Anulada`

## Estados de orden_trabajo y su relación con proforma
`Pendiente de Diagnóstico` → `Diagnóstico Finalizado` → `En Proceso` → `Finalizada` / `Anulada`

Cadena real (importante, no es lo que parece a primera vista):
1. La OT se crea al registrar la unidad (CU-04), **sin proforma vinculada**, estado `Pendiente de Diagnóstico`.
2. Se hace el diagnóstico (CU-05) → OT pasa a `Diagnóstico Finalizado`, **sigue sin proforma**.
3. Se elabora y guarda la proforma (CU-06), nace en `Borrador` → **recién aquí se vincula** `nro_proforma` a la OT, pero la OT sigue en `Diagnóstico Finalizado`.
4. La proforma se edita/emite (`Emitida`) → OT sigue sin cambio.
5. La proforma se aprueba (`ProformaController::actualizarEstado`) → **recién aquí** la OT pasa a `En Proceso` y se confirma el vínculo `nro_proforma`.

`OrdenTrabajoController::obtenerOrdenes()` (la vista web normal) filtra solo OT con `proforma.estado = 'Aprobada'` — por eso una OT en `Pendiente de Diagnóstico`/`Diagnóstico Finalizado` nunca debería aparecer ahí (no tiene proforma Aprobada todavía, por diseño).

## Sidebar actual (ordenado por frecuencia de uso)
- General: Dashboard (+ widget de Reportes por Voz, CU-22)
- Atención al cliente: Clientes, Vehículos
- Operaciones: Ingresos, Órdenes de Trabajo, Préstamos
- Consultas: Historial, Proformas
- Catálogos: Taller
- Administración: Usuarios, Roles, Privilegios, Bitácora
- Personal: Contratos Laborales, Liquidar Sueldos

## PDFs implementados
- ✅ Cotización/Proforma (dompdf)
- ✅ Factura final (dompdf)
- ✅ Reporte por comando de voz, exportación genérica (dompdf + Maatwebsite/Excel)

## PDFs pendientes
- 🔜 Diagnóstico técnico
- 🔜 Orden de Trabajo
- 🔜 Historial del vehículo

## Roadmap completo
| Ciclo | Enfoque | Estado |
|---|---|---|
| Ciclo 1 | Base y seguridad (CU-01/02/03/13/19/20/21) | ✅ Completo |
| Ciclo 2 | Recepción y presupuesto (CU-04/05/06/07/08) | ✅ Completo |
| Ciclo 3 | Gestión operativa (CU-14/15/16/09/10) | ✅ Completo |
| Ciclo 4 | Liquidación y salida (CU-17/18/11/12) + Reportes por voz (CU-22) | ✅ Completo |

## Dependencias entre ciclos
- Ciclo 3 requiere: proforma Aprobada (Ciclo 2)
- Ciclo 4 requiere: OT con repuestos y mano de obra registrados (Ciclo 3)

## Git
- Trabajamos en la misma rama por ahora
- Hacer `git pull` antes de empezar cada sesión
- Commitear seguido con mensajes claros
- Avisar al compañero antes de mergear cambios grandes

## CU-22 — Reportes por Comando de Voz (arquitectura)

### Flujo end-to-end (qué llama a qué, en orden)
1. **Frontend** (`public/js/reporte-voz.js`, cargado desde `resources/views/dashboard/index.blade.php`): botón "Hablar" → `MediaRecorder` grava audio, forzando `audio/ogg;codecs=opus` (con fallback) porque el default del navegador (`audio/webm`) no es aceptado por Gemini.
2. El audio se manda como `multipart/form-data` a `POST /api/reporte/consultar` → `ReporteController::consultarReporte()`.
3. El controller delega en `App\Services\ServicioInterpretacionIA::interpretar($audioBase64, $mimeType)` — **primera llamada a Gemini** (modelo `gemini-2.5-flash`, `generateContent` con `inlineData` de audio + `tools.functionDeclarations` cargado desde `app/Services/function_declarations.json`). Gemini transcribe el audio y devuelve `{transcripcion, nombre, parametros, error}` — nunca ejecuta nada, solo decide.
4. `ReporteController` valida `$nombreFuncion` contra `config('reporte_voz')` (la whitelist) y verifica `auth()->user()->puede($definicion['permiso'])` — si no pasa cualquiera de los dos, corta ahí sin tocar la BD.
5. Si pasa, ejecuta `call_user_func_array([app($definicion['controller']), $definicion['metodo']], $parametros)` — esto invoca un método real ya existente en un controller del taller (ej. `FacturaController::buscarFacturasPorPeriodo()`), que sí toca Eloquent/la BD.
6. El resultado se normaliza con `json_decode(json_encode($resultado), true)` (resuelve Collections de Eloquent a array plano) y se manda a `ServicioInterpretacionIA::generarAudioRespuesta($mensaje)` — **segunda llamada a Gemini**, modelo TTS distinto (`gemini-3.1-flash-tts-preview`). Devuelve audio PCM crudo (`audio/L16;codec=pcm;rate=24000`), que se envuelve manualmente con un encabezado WAV de 44 bytes (`ServicioInterpretacionIA::pcmAWav()`) antes de mandarlo al frontend — el navegador no reproduce PCM sin ese encabezado.
7. El JSON de respuesta (`ok, transcripcion, mensaje, funcion, parametros, resultado, audio_base64, audio_mime`) llega al frontend, que renderiza el resultado de forma genérica (tabla si es lista, clave-valor si es objeto plano) y reproduce el audio.
8. **Exportación** (botones PDF/Excel): el frontend manda `funcion` + `parametros` (no el resultado) a `POST /api/reporte/exportar` → `ReporteController::exportarReporte()` **re-ejecuta la misma función desde cero** (con su misma verificación de permiso) y genera el archivo con `\PDF::loadView('reporte_voz.export_pdf', ...)` o `Maatwebsite\Excel\Facades\Excel::download(new ReporteVozExport($resultado), ...)` — nunca confía en datos ya mostrados en pantalla.

### Archivos involucrados
| Archivo | Rol |
|---|---|
| `app/Services/ServicioInterpretacionIA.php` | Las dos llamadas a Gemini (interpretar + TTS) + conversión PCM→WAV |
| `app/Services/function_declarations.json` | Schema de las 14 funciones, formato `functionDeclarations` de Gemini (camelCase) |
| `config/reporte_voz.php` | Whitelist función→controller→método→permiso. Única fuente de verdad de seguridad |
| `app/Http/Controllers/ReporteController.php` | Orquesta todo: recibe audio, verifica permiso, ejecuta, exporta |
| `app/Exports/ReporteVozExport.php` | Normaliza cualquier resultado a filas de Excel, con ancho de columna dinámico |
| `resources/views/reporte_voz/export_pdf.blade.php` | Vista genérica para el PDF (dompdf) |
| `resources/views/dashboard/index.blade.php` | HTML del widget (tarjeta "Preguntá algo sobre el taller") |
| `public/js/reporte-voz.js` | Grabación, fetch, renderizado, exportación vía formulario |
| `routes/web.php` | `POST /api/reporte/consultar` y `POST /api/reporte/exportar`, ambas bajo `auth` + `permiso:CU22_GEN` |

### Catálogo completo de funciones (config/reporte_voz.php)
| Función | Controller | Permiso requerido |
|---|---|---|
| `buscarProformasPorEstado` | `ProformaController` | `CU06_BUS` |
| `buscarProformaPorNumero` | `ProformaController` | `CU06_BUS` |
| `buscarFacturasPorPeriodo` | `FacturaController` | `CU17_BUS` |
| `buscarFacturaPorNumero` | `FacturaController` | `CU17_BUS` |
| `buscarCuotasPendientes` | `CuotaController` | `CU18_BUS` |
| `buscarOrdenesPorEstado` | `OrdenTrabajoController` | `CU14_BUS` |
| `buscarPrestamos` | `PrestamoController` | `CU09_BUS` |
| `buscarAsignacionesPorOrden` | `AsignacionController` | `CU15_BUS` |
| `buscarDiagnosticosPorPeriodo` | `DiagnosticoController` | `CU05_BUS` |
| `buscarCatalogo` | `CatalogoController` | ninguno (solo `CU22_GEN`) |
| `buscarAutosCatalogo` | `AutoController` | ninguno |
| `buscarTiposTrabajadorCatalogo` | `CargoController` | ninguno |
| `contarClientesPorZona` | `ClienteController` | ninguno — solo conteo, no lista nombres |
| `contarPersonalPorTipoTrabajador` | `CargoController` | ninguno — solo conteo |

Todos los métodos de la tabla son **wrappers nuevos** (no existían antes de CU-22), creados específicamente para ser invocados desde aquí — no tienen ruta ni vista propia, viven en los controllers existentes para reutilizar el modelo Eloquent y las reglas de negocio sin duplicarlas.

### Reglas de seguridad (no negociables si se modifica este flujo)
- `CU22_GEN` jamás autoriza un dato por sí mismo — solo abre el micrófono.
- Si un nombre de función no está en `config('reporte_voz')`, se rechaza, aunque Gemini lo haya inventado.
- El permiso se verifica **siempre antes de ejecutar**, tanto en consulta como en exportación — nunca se confía en el estado del frontend.
- Gemini nunca recibe ni genera SQL/Eloquent — solo nombres de función + parámetros tipados, validados por el `function_calling_config.mode = 'VALIDATED'` + `allowed_function_names`.

### Detalles técnicos que rompen si se tocan sin cuidado
- `properties: []` en el JSON de una función sin parámetros se serializa mal en PHP (lista en vez de objeto) y Gemini la rechaza con 400 — `ServicioInterpretacionIA` lo corrige forzando `new \stdClass()` en el constructor.
- En Windows/XAMPP, Guzzle puede quedarse esperando indefinidamente (timeout sin error claro) por el almacén de certificados SSL — se fuerza `CURLOPT_SSL_OPTIONS => CURLSSLOPT_NATIVE_CA`.
- Las llamadas a Gemini usan `->retry(2, 1500, ..., throw: false)` ante un 503 ("modelo saturado") — el `throw: false` es obligatorio, sin él Laravel relanza una excepción no controlada en vez de dejar que `$respuesta->failed()` la maneje.
- Maatwebsite/Excel requiere las extensiones PHP `gd`, `zip`, `dom`, `simplexml`, `xml`, `xmlreader`, `xmlwriter` — deben estar declaradas en `composer.json` (`require`) para que Railway/Nixpacks las active en el build.

- **Pendiente**: el mensaje hablado de éxito es un texto fijo ("Aquí está el resultado de tu consulta"), no personalizado según el resultado.

## Pendientes conocidos (no urgentes)
- Permiso `CU13_PRI` gatea la sección "Catálogos" del sidebar de forma provisional — no existe un permiso propio para gestión de catálogo del taller.
- Tabla `recoge` existe en el esquema pero ningún CU la llena todavía (posible CU-23 futuro: registrar entrega de vehículo).
- Extremo 2 (constructor de filtros genérico para CU-22, en vez de catálogo fijo de funciones) — evaluado y descartado por tiempo, queda como mejora futura.

## Diagrama de despliegue - ✅ Completado
- Cliente: Dispositivo Desktop + Dispositivo Móvil
- Servidor local: Laravel & PHP → MySQL Local (TCP/IP)
- Servidor nube: Railway Laravel & PHP → Railway MySQL (TCP/IP)
- Servidor externo: Gemini API y Stripe (HTTPS) — conectados desde ambos servidores (local y Railway)

## Diagrama de paquetes en capas - ✅ Completado
- Capa Específica: P1, P2, P3, P4
- Capa General: P3 Gestión Administrativa (transversal)
- Capa Intermedia: Laravel Blade, Laravel Core MVC, Eloquent ORM, Navegador
- Capa Software del Sistema: HTTPS/TCP-IP, Railway, MySQL SGBD

## Convenciones de documentación — Fichas de Caso de Uso

- Propósito y Descripción: nunca mencionar CU específicos (ej. "CU-04"), solo describirlos en lenguaje natural
- Flujo principal: nunca mencionar CU específicos, solo acciones concretas
- Pre/Postcondición: pueden ser "Ninguna" si no aplica — no rellenar por rellenar
- Pre/Postcondición: solo mencionar un CU si la relación es directa e importante (ej. precondición = CU-14, porque viene de un include)
- Excepción: puede ser "Ninguna" si no aplica
- Los pasos del flujo principal deben redactarse pensando en que serán métodos reales en el código

## Convenciones de documentación — Diagramas de Caso de Uso

- Include: solo si la acción es obligatoria y parte del flujo directo
- Extend: solo si la acción es opcional
- No incluir CU-19 (Iniciar Sesión) como include — es obvio y no se grafica
- Solo agregar relaciones si son muy necesarias o directas

## Convenciones de documentación — Diagramas de Comunicación

- Estructura: Actor → Vista → Controller → Entidad(es)
- Símbolos: Actor (monigote), Vista (interfaz/boundary), Controller (círculo con flecha), Entidad (círculo simple)
- Formato de mensajes: una cadena por acción del actor: 1, 1.1, 1.2, 1.3 / 2, 2.1, 2.2...
- Sin mensajes de retorno, solo de ida
- Paréntesis siempre vacíos: listarOrdenes(), no listarOrdenes(nro)
- El Controller se conecta directo a cada entidad; las entidades no se relacionan entre sí
- Cada cadena llega hasta todas las entidades que esa acción necesita consultar/modificar
- Las entidades corresponden a tablas/modelos del diagrama de clases
- Agregar entidades adicionales solo si esa acción consulta o modifica datos de otra tabla
- Las entidades de catálogo (ej. Repuesto, ManoObra, Herramienta) solo se consultan/actualizan en las cadenas donde realmente se las toca — no en eliminar si solo se elimina el detalle
- Los mensajes piensan en CRUD: 1 crear, 2 ver/listar, 3 modificar, 4 eliminar — no siempre aplican los 4
- El nombre del actor en el diagrama debe coincidir con el actor iniciador definido en la ficha del caso de uso
- Debajo de cada diagrama de comunicación va un breve resumen en texto (2-4 oraciones) describiendo el flujo del caso de uso en lenguaje natural

## Convenciones de documentación — Análisis de Clase

- Los métodos del análisis de clase deben coincidir con los mensajes del diagrama de comunicación
- Los métodos del controller en código Laravel deben reflejar esos mismos nombres
- Estructura: Actor → Vista → Controller → Entidad principal → Entidades secundarias
- Vista: atributos = campos visibles/llenables en pantalla (sin tipos de dato, sin id internos, sin clave); métodos = acciones que el actor dispara
- Controller: sin atributos; métodos = exactamente los mensajes del diagrama de comunicación
- Entidad principal: la tabla que el CU modifica o consulta directamente; el controller apunta a ella
- Entidades secundarias: se relacionan con la entidad principal reflejando las FK del diagrama de clases, no con el controller directamente
- Bitácora siempre se relaciona con la entidad principal del CU, no con el controller
- Sin tipos de dato en atributos
- Sin getters/setters ni métodos técnicos de implementación

## Convenciones de documentación — Diagrama de Secuencia

- Lifelines: Actor, Vista, Controller, Entidades (mismos participantes que análisis de clase)
- Todos los lifelines usan el mismo símbolo: rectángulo arriba + línea punteada abajo
- Solo mensajes de ida (flechas sólidas), sin mensajes de retorno punteados
- Numeración lineal global y secuencial (1, 2, 3, 4...)
- Los mensajes deben coincidir con los del diagrama de comunicación (mismos nombres)
- CORRECCIÓN PARA CICLO 2: los mensajes deben basarse en el análisis de clase, no en el diagrama de comunicación — el controller llama solo a la entidad principal, las entidades secundarias se comunican entre sí
- Barras de activación opcionales (la ingeniería no es rigurosa con eso)
- Fragmentos combinados: usar `alt` para flujos alternativos, con guardas entre corchetes [condición]
- Para CUs simples: 1 diagrama por CU
- Para CUs de gestión (CRUD): 1 diagrama con alt para cada acción (registrar, editar, dar de baja)
- El flujo de listar va fuera del alt como flujo principal
- Etiqueta del contenedor: `interaction NombreCU`

## Convenciones de respuesta
- Respuestas cortas y directas
- No reescribir archivos completos — solo edits puntuales
- Sin preámbulo extenso ni narración del plan
- Debatir proactivamente cuando algo importa
- Commitear por funcionalidad, no todo junto
- Recordar hacer git pull al inicio de cada sesión de trabajo