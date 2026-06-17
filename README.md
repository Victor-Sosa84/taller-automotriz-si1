# Sistema de Gestión — Taller Mecánico Automotriz JECOES Tronic

Sistema web desarrollado con **Laravel 11** para la gestión interna del Taller Mecánico Automotriz JECOES Tronic. Implementado siguiendo la metodología **PUDS** (Proceso Unificado de Desarrollo de Software) en 4 ciclos iterativos.

---

## 🌐 Sistema desplegado en producción

**URL:** https://taller-automotriz-si1-production.up.railway.app

> El sistema está desplegado en **Railway** con base de datos MySQL. Las instrucciones de instalación a continuación son para entorno **local de desarrollo**.

---

## Tecnologías utilizadas

- **Backend:** PHP 8.2 + Laravel 11
- **Base de datos:** MySQL
- **Frontend:** Blade Templates + CSS personalizado (tema oscuro automotriz)
- **Autenticación:** Laravel Auth + Breeze con RBAC dinámico
- **Email:** Mailtrap (desarrollo) / SMTP real (producción)
- **Despliegue:** Railway (app + MySQL)

---

## Ciclos de desarrollo (PUDS)

| Ciclo | Casos de Uso | Estado |
|---|---|---|
| **Ciclo 1** — Base y Seguridad | CU-01, CU-02, CU-03, CU-13, CU-19, CU-20, CU-21 | ✅ Completado |
| **Ciclo 2** — Recepción y Presupuesto | CU-04, CU-05, CU-06, CU-07, CU-08 | ✅ Completado |
| **Ciclo 3** — Gestión Operativa | CU-09, CU-10, CU-14, CU-15, CU-16 | ✅ Completado |
| **Ciclo 4** — Liquidación y Salida | CU-11, CU-12, CU-17, CU-18 | ⏳ Pendiente |

---

## Módulos implementados (Ciclo 1 y 2)

| Módulo | Descripción | Privilegios requeridos |
|---|---|---|
| Autenticación | Login por correo o usuario, recuperación de contraseña | CU19, CU20 |
| Dashboard | Vista genérica con stats según privilegios | — |
| Usuarios | CRUD con nombre de usuario automático | CU13_ADD/MOD/ELI/BUS |
| Perfiles/Roles | CRUD dinámico de roles de acceso | CU13_PRI |
| Privilegios | Gestión granular por Paquete → CU → Permiso | CU13_PRI |
| Bitácora | Auditoría completa de acciones del sistema | CU21_BUS |
| Clientes | Registro y edición (CU-01) | CU01_ADD/MOD/BUS |
| Vehículos | Ficha técnica con validación de placa (CU-02) | CU02_ADD/MOD/ELI/BUS |
| Historial | Consulta de diagnósticos y OT por vehículo (CU-03) | CU03_BUS |
| Ingresos | Registro de entrada de vehículos al taller (CU-04) | CU04_ADD/MOD/BUS |
| Diagnóstico | Registro de fallas técnicas por mecánico (CU-05) | CU05_ADD/MOD/BUS |
| Proforma | Elaboración de presupuesto con repuestos y MO (CU-06) | CU06_ADD/MOD/BUS/ELI |
| Cotización | Emisión del documento formal al cliente (CU-07) | CU07_ADD |
| Estado Proforma | Gestión de estados Borrador→Emitida→Aprobada/Observada/Anulada (CU-08) | CU08_MOD |
| Órdenes de Trabajo | Gestión del ciclo de vida de OTs vinculadas a proformas aprobadas (CU-14) | CU14_BUS/MOD |
| Asignaciones | Asignación de responsables a tareas de una OT (CU-15) | CU15_BUS/ADD/MOD |
| Detalles OT | Registro de repuestos y mano de obra ejecutada (CU-16) | CU16_BUS/ADD/MOD/DEL |
| Préstamos | Registro y seguimiento de préstamos de herramientas (CU-09) | CU09_BUS/ADD/DEL |
| Estado Herramientas | Registro de devoluciones y actualización de estado (CU-10) | CU10_MOD |
| Catálogos | Gestión de repuestos, MO, herramientas, tipos y marcas, con precio/costo referencial | CU13_PRI |

---

## Sistema de privilegios (RBAC dinámico)

El sistema implementa **RBAC dinámico** basado en casos de uso PUDS:

- Los **perfiles** (roles) se crean y eliminan desde la interfaz — no están hardcodeados
- Los **privilegios** se organizan por: **Paquete → Caso de Uso → Permiso granular**
- El **usuario id=1** (Admin Principal) tiene acceso total sin restricciones
- Los cambios de privilegios se aplican de inmediato sin reiniciar el servidor

**Paquetes de privilegios:**
- P1: Gestión de Recepción
- P2: Gestión Comercial y Facturación
- P3: Gestión Administrativa
- P4: Gestión Operativa

---

## Requisitos previos (desarrollo local)

- PHP **8.2** o superior
- **Composer**
- **MySQL** (XAMPP, Laragon, o similar)
- Cuenta en **Mailtrap** (para recuperación de contraseña)

```bash
php --version
composer --version
```

---

## Instalación local paso a paso

### 1. Clonar el repositorio
```bash
git clone https://github.com/Victor-Sosa84/taller-automotriz-si1.git
cd taller-automotriz-si1
```

### 2. Instalar dependencias
```bash
composer install
```

### 3. Configurar el entorno
```bash
cp .env.example .env
php artisan key:generate
```

Edita el `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_taller_si1
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=false

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu_username_mailtrap
MAIL_PASSWORD=tu_password_mailtrap
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@taller.com"
MAIL_FROM_NAME="Taller Automotriz"
```

> Crea la base de datos `db_taller_si1` en phpMyAdmin antes de continuar.

### 4. Migraciones y seeders
```bash
php artisan migrate --seed
```

### 5. Levantar el servidor
```bash
php artisan serve
```

Abre **http://localhost:8000**

---

## Usuario de prueba (local)

| Usuario | Correo | Contraseña | Acceso |
|---|---|---|---|
| `aprincipal` | `admin@taller.com` | `Admin1234!` | Total (id=1, sin restricciones) |

> El sistema es dinámico — crea perfiles adicionales desde la interfaz y asígnales privilegios.

---

## Recuperación de contraseña

1. Ve a `/login` → *¿Olvidaste tu contraseña?*
2. Ingresa el correo del usuario
3. Abre tu bandeja en [mailtrap.io](https://mailtrap.io) y copia el enlace
4. Ingresa y confirma la nueva contraseña

---

## Estructura del proyecto

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/                  ← Login, logout, reset password
│   │   ├── DashboardController    ← Dashboard genérico por privilegios
│   │   ├── UsuarioController      ← CU-13: Gestionar Usuarios
│   │   ├── RolController          ← CU-13: Gestionar Perfiles
│   │   ├── PermisoController      ← CU-13: Gestionar Privilegios
│   │   ├── ClienteController      ← CU-01: Gestionar Cliente
│   │   ├── AutoController         ← CU-02: Gestionar Ficha Técnica
│   │   ├── HistorialController    ← CU-03: Consultar Historial
│   │   ├── CargoController        ← Tipos de trabajo del personal
│   │   ├── OrdenTrabajoController     ← CU-04: Gestionar Ingreso y CU-14: Gestionar OT
│   │   ├── DiagnosticoController      ← CU-05: Realizar Diagnóstico
│   │   ├── ProformaController         ← CU-06/07/08: Proforma
│   │   ├── BitacoraController         ← CU-21: Consultar Bitácora
│   │   ├── AsignacionController       ← CU-15: Asignar Responsables
│   │   ├── DetalleOTController        ← CU-16: Registrar Repuestos/MO
│   │   ├── PrestamoController         ← CU-09: Préstamo Herramientas
│   │   ├── HerramientaController      ← CU-10: Estado Herramientas
│   │   └── CatalogoController         ← Catálogos del sistema
│   ├── Middleware/
│   │   └── CheckPermiso.php       ← Verificación de privilegios por CU
│   └── Requests/Auth/LoginRequest ← Login por correo o nombre de usuario
├── Models/                        ← Un modelo por tabla
database/
├── migrations/                    ← Estructura completa de 32 tablas
└── seeders/                       ← Datos iniciales por ciclo
resources/views/
├── layouts/app.blade.php          ← Layout con sidebar dinámico por privilegios
├── auth/                          ← Login, forgot-password, reset-password
├── dashboard/                     ← Dashboard genérico
├── usuarios/                      ← CRUD + cargos laborales
├── roles/                         ← CRUD de perfiles dinámicos
├── permisos/                      ← Gestión por Paquete → CU → Privilegio
├── clientes/                      ← CU-01
├── autos/                         ← CU-02
├── historial/                     ← CU-03
├── bitacora/                      ← CU-21
├── ingresos/                      ← CU-04
├── diagnosticos/                  ← CU-05
├── proformas/                     ← CU-06, CU-07, CU-08
├── orden_trabajo/                 ← CU-14
├── asignacion/                    ← CU-15
├── detalle_ot/                    ← CU-16
├── prestamo/                      ← CU-09, CU-10
└── catalogo/                      ← Catálogos
routes/
├── web.php                        ← Rutas protegidas por privilegio
└── auth.php                       ← Rutas de autenticación
```

---

## Comandos útiles

```bash
# Reiniciar BD y resembrar (desarrollo local)
php artisan migrate:fresh --seed

# Agregar permisos de nuevo ciclo sin borrar asignaciones
php artisan db:seed --class=PermisoSeeder --force

# Ver todas las rutas
php artisan route:list

# Limpiar caché
php artisan config:clear && php artisan cache:clear && php artisan route:clear

# Vaciar sesiones
php artisan tinker
# Luego: DB::table('sessions')->truncate(); exit
```

---

## Notas importantes

- El `.env` **nunca** se sube al repositorio — cada desarrollador crea el suyo desde `.env.example`.
- En **producción** (Railway): `SESSION_SECURE_COOKIE=true` y `SESSION_SAME_SITE=lax` son obligatorios para que funcione en dispositivos móviles.
- Los privilegios del Ciclo 3 y 4 se agregarán al `PermisoSeeder` usando `insertOrIgnore` — sin borrar asignaciones existentes.
- El usuario `id=1` es el Admin Principal intocable — no se puede eliminar ni modificar desde la interfaz.
- El estado **"Vencida"** de una proforma no se guarda en la base de datos: se calcula dinámicamente (`Proforma::estado_visual`) cuando el plazo de validez ya pasó y el estado real es Emitida u Observada.