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

## Paquetes
| Paquete | CU incluidos |
|---|---|
| P1 Gestión de Recepción | CU-01, CU-02, CU-03, CU-04, CU-05 |
| P2 Gestión Comercial y Facturación | CU-06, CU-07, CU-08, CU-17, CU-18 |
| P3 Gestión Administrativa | CU-11, CU-12, CU-13, CU-19, CU-20, CU-21 |
| P4 Gestión Operativa | CU-09, CU-10, CU-14, CU-15, CU-16 |

## Diagrama de clases — entidades principales
| Entidad | Atributos clave |
|---|---|
| `persona` | ci (PK), nombre, telefono, direccion, es_cliente, es_personal |
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
| `prestamo_herramienta` | id (PK), fecha_salida, fecha_devolucion |
| `detalle_prestamo` | id_prestamo_herramienta (FK), nro_herramienta (FK) — PK compuesta, estado_salida, estado_retorno |
| `auto` | placa (PK), marca, modelo, anio, color |
| `diagnostico` | id (PK), fecha, ci_personal (FK), placa_auto (FK) |
| `detalle_diagnostico` | id_diagnostico (FK), id_detalle_diagnostico — PK compuesta, descripcion |
| `mano_obra` | id (PK), descripcion |
| `proforma` | nro (PK), ci_cliente (FK), id_diagnostico (FK), fecha, total_aprox, estado, plazo |
| `repuesto` | id (PK), nombre, estado, marca |
| `proforma_repuesto` | nro_proforma (FK), id_repuesto (FK) — PK compuesta, cantidad, precio_unitario, descuento |
| `proforma_servicio` | nro_proforma (FK), id_mano_obra (FK) — PK compuesta, costo, estado, cantidad |
| `orden_trabajo` | nro (PK), nro_proforma (FK), fecha_inicio, fecha_fin, estado, kilometraje, observacion_entrada, observacion_salida |
| `detalle_repuesto` | nro_orden_trabajo (FK), id_repuesto (FK) — PK compuesta, cantidad, precio_unitario, descuento |
| `detalle_trabajo` | nro_orden_trabajo (FK), id_mano_obra (FK) — PK compuesta, costo, estado, cantidad |
| `realiza` | ci_personal (FK), nro_orden_trabajo (FK), id_mano_obra (FK) — PK compuesta, tipo_participacion |
| `recoge` | nro_orden_trabajo (FK), ci_persona (FK) — PK compuesta, relacion, fecha |
| `factura` | nro (PK), fecha_emision, nit, nombre, total, plazo |
| `detalle_factura` | id_detalle_factura (PK), nro_factura (FK), descripcion, tipo, cantidad, precio, precio_unitario |
| `cuota` | nro_factura (FK), nro_cuota — PK compuesta, monto, fecha |
| `bitacora` | id (PK), id_usuario (FK), fecha_hora, accion, ip_equipo |

## Arquitectura clave
- RBAC dinámico: permisos formato `CU04_ADD`, middleware `CheckPermiso`
- Usuario `id=1` (aprincipal) bypassa todo sin consultar BD
- Sin `$timestamps` en la mayoría de modelos
- Tablas sin prefijo, PKs personalizadas (`nro`, `ci`, `placa`)
- Permisos: Paquete → Caso de Uso → Permiso granular (ADD/MOD/ELI/BUS)

## Ciclos completados
- **Ciclo 1:** CU-01 a CU-03, CU-13, CU-19, CU-20, CU-21
- **Ciclo 2:** CU-04, CU-05, CU-06, CU-07, CU-08 + PDF de cotización con dompdf
- **Ciclo 3:** CU-09, CU-10, CU-14, CU-15, CU-16 + Catálogo Taller

## Flujo principal implementado
Ingresos (CU-04) → Diagnóstico (CU-05) → Proforma (CU-06) → Emitir/Estado (CU-07/08) → OT (CU-14) → Asignación (CU-15) → Detalles (CU-16)

## Modelos existentes
`Usuario`, `Persona`, `Cliente`, `Personal`, `Auto`, `OrdenTrabajo`, `Diagnostico`, `DetalleDiagnostico`, `Proforma`, `ProformaRepuesto`, `ProformaServicio`, `Repuesto`, `ManoObra`, `DetalleRepuesto`, `DetalleTrabajo`, `Bitacora`,
`Realiza`, `PrestamoHerramienta`, `DetallePrestamo`, `Herramienta`, `TipoHerramienta`, `MarcaHerramienta`

## Estados de proforma
`Borrador` → `Emitida` → `Aprobada` / `Observada` / `Anulada`

## Sidebar actual
- General: Dashboard
- Consultas: Historial, Proformas
- Administración: Usuarios, Roles, Privilegios, Bitácora
- Atención al cliente: Clientes, Vehículos
- Operaciones: Ingresos, Órdenes de Trabajo, Préstamos
- Catálogos: Taller

## PDFs implementados
- ✅ Cotización/Proforma (dompdf)

## PDFs pendientes
- 🔜 Diagnóstico técnico
- 🔜 Orden de Trabajo (Ciclo 3)
- 🔜 Historial del vehículo (post Ciclo 3)

## Ciclo 4 — pendiente (Liquidación y Salida)
- CU-11: Gestionar Contratos de Trabajo
- CU-12: Liquidar Pagos de Personal
- CU-17: Generar Factura Final
- CU-18: Registrar Pago y Cuotas

## Roadmap completo
| Ciclo | Enfoque | Estado |
|---|---|---|
| Ciclo 1 | Base y seguridad (CU-01/02/03/13/19/20/21) | ✅ Completo |
| Ciclo 2 | Recepción y presupuesto (CU-04/05/06/07/08) | ✅ Completo |
| Ciclo 3 | Gestión operativa (CU-14/15/16/09/10) | ✅ Completo |
| Ciclo 4 | Liquidación y salida (CU-17/18/11/12) | ⏳ Pendiente |

## Dependencias entre ciclos
- Ciclo 3 requiere: proforma Aprobada (Ciclo 2)
- Ciclo 4 requiere: OT con repuestos y mano de obra registrados (Ciclo 3)

## Git
- Trabajamos en la misma rama por ahora
- Hacer `git pull` antes de empezar cada sesión
- Commitear seguido con mensajes claros
- Avisar al compañero antes de mergear cambios grandes

## Notas del Ciclo 4 — Documentación pendiente

### Pasarela de pago
- Usar Libélula QR + Pago con tarjeta (obligatorio para Ciclo 4)

### Implementación (Solo Ciclo 4 / Segunda presentación)
- **5.1 Elección de plataforma:** describir PHP/Laravel (ventajas/desventajas), MySQL, Windows, dompdf. En "Otros": software adicional necesario (lector PDF, etc.)
- **5.2 Diagrama de componentes general** — recursos físicos del sistema
- **5.3 Diagrama de componentes por paquete** — uno por cada paquete (P1, P2, P3, P4)

## Diagrama de despliegue - ✅ Completado
- Cliente: Dispositivo Desktop + Dispositivo Móvil
- Servidor local: Laravel & PHP → MySQL Local (TCP/IP)
- Servidor nube: Railway Laravel & PHP → Railway MySQL (TCP/IP)

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