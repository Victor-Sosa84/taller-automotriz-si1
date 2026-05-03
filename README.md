# Sistema de Gestión — Taller Mecánico Automotriz

Sistema web desarrollado con **Laravel 11** para la gestión interna de un taller automotriz. Cubre la gestión de personal, clientes, vehículos, historial de mantenimiento, permisos por rol y auditoría completa de acciones.

---

## Tecnologías utilizadas

- **Backend:** PHP 8.2 + Laravel 11
- **Base de datos:** MySQL
- **Frontend:** Blade Templates + CSS personalizado (tema oscuro automotriz)
- **Autenticación:** Laravel Auth + Breeze (roles, permisos y recuperación de contraseña)
- **Email:** Mailtrap (entorno de desarrollo)

---

## Módulos implementados

| Módulo | Descripción | Roles con acceso |
|---|---|---|
| Autenticación | Login por correo o nombre de usuario, recuperación de contraseña vía email | Todos |
| Dashboard | Vista personalizada por rol tras el login | Todos |
| Usuarios | CRUD con generación automática de nombre de usuario | Admin |
| Cargos | Asignación de tipos de trabajo al personal | Admin |
| Permisos | Activación/desactivación de permisos por rol con toggles | Admin |
| Bitácora | Registro de todas las acciones del sistema (login, CRUD, cambios) | Admin |
| Clientes | Registro y edición (sin eliminación) | Admin, Recepcionista |
| Vehículos | Ficha técnica, eliminación protegida si tiene diagnósticos | Admin, Recepcionista |
| Historial | Diagnósticos y órdenes de trabajo por vehículo | Todos |

> Módulos pendientes de interfaz (estructura en BD lista): diagnósticos, proformas, órdenes de trabajo, herramientas, facturación, contratos.

---

## Roles y permisos

El sistema usa RBAC (Role-Based Access Control) con 3 roles y 32 permisos distribuidos en 7 módulos:

| ID | Rol | Acceso |
|---|---|---|
| 1 | Administrador | Todo — incluyendo usuarios, bitácora y gestión de permisos |
| 2 | Mecánico Jefe | Diagnósticos, órdenes de trabajo, herramientas, historial |
| 3 | Recepcionista | Clientes, vehículos, proformas, facturación, historial |

Los permisos se gestionan desde el panel de administración → sección Permisos, con toggles por módulo y rol. Los cambios se aplican de inmediato.

---

## Requisitos previos

- PHP **8.2** o superior
- **Composer**
- **MySQL** (XAMPP, Laragon, o similar)
- Cuenta en **Mailtrap** (gratuita, para recuperación de contraseña)

```bash
php --version
composer --version
```

---

## Instalación paso a paso

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
> Las credenciales de Mailtrap se obtienen en mailtrap.io → Sandboxes → My Sandbox → SMTP Setting → Laravel 9+.

### 4. Migraciones y seeders
```bash
php artisan migrate --seed
```

Crea las 32 tablas y carga: roles, 32 permisos con asignación por rol, tipos de trabajador, marcas y tipos de herramienta, y 3 usuarios de prueba.

### 5. Levantar el servidor
```bash
php artisan serve
```

Abre **http://localhost:8000**

---

## Usuarios de prueba

| Rol | Usuario | Correo | Contraseña |
|---|---|---|---|
| Administrador | `aprincipal` | `admin@taller.com` | `Admin1234!` |
| Mecánico Jefe | `mprueba` | `mecanico@taller.com` | `Mecanico1234!` |
| Recepcionista | `rprueba` | `recepcion@taller.com` | `Recepcion1234!` |

> Puedes ingresar con el **correo** o con el **nombre de usuario** — ambos funcionan.

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
│   │   ├── DashboardController    ← Redirección por rol
│   │   ├── UsuarioController      ← CRUD usuarios + bitácora
│   │   ├── ClienteController      ← CRUD clientes + bitácora
│   │   ├── AutoController         ← CRUD vehículos + bitácora
│   │   ├── HistorialController    ← Historial de mantenimiento
│   │   ├── CargoController        ← Asignación tipos de trabajo
│   │   ├── PermisoController      ← Gestión de permisos por rol
│   │   └── BitacoraController     ← Registro de actividad
│   ├── Middleware/
│   │   ├── CheckRol.php           ← Protección por rol (dashboards)
│   │   └── CheckPermiso.php       ← Protección por permiso (módulos)
│   └── Requests/Auth/LoginRequest ← Login por correo o usuario
├── Models/                        ← Un modelo por tabla principal
database/
├── migrations/                    ← 32 tablas + migraciones de alteración
└── seeders/                       ← Datos iniciales completos
resources/views/
├── layouts/app.blade.php          ← Layout con sidebar dinámico por permiso
├── auth/                          ← Login, forgot-password, reset-password
├── dashboard/                     ← admin, mecanico, recepcionista
├── usuarios/                      ← CRUD + cargos
├── permisos/                      ← Gestión de permisos por rol con toggles
├── clientes/                      ← CRUD + perfil con vehículos e historial
├── autos/                         ← CRUD vehículos
├── historial/                     ← Búsqueda e historial por vehículo
└── bitacora/                      ← Tabla de auditoría con filtros
routes/
├── web.php                        ← Rutas protegidas por rol y permiso
└── auth.php                       ← Rutas de autenticación (Breeze)
```

---

## Comandos útiles

```bash
# Reiniciar BD y resembrar desde cero
php artisan migrate:fresh --seed

# Seeders individuales
php artisan db:seed --class=AdminSeeder
php artisan db:seed --class=PermisoSeeder

# Ver todas las rutas
php artisan route:list

# Limpiar caché
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Vaciar sesiones activas
php artisan tinker
# Una vez adentro de tinker escribes:
DB::table('sessions')->truncate();
exit
```

---

## Notas importantes

- El `.env` **nunca** se sube al repositorio — cada desarrollador crea el suyo desde `.env.example`.
- Las contraseñas del seeder son solo para **desarrollo** — cambiarlas antes de producción.
- Para despliegue en la nube se recomienda mantener `SESSION_DRIVER=database` y configurar un SMTP real en lugar de Mailtrap.
