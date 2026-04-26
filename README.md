# Sistema de Gestión — Taller Mecánico Automotriz

Sistema web desarrollado con **Laravel 11** para la gestión interna de un taller automotriz. Permite administrar usuarios del personal, registrar clientes, gestionar vehículos, consultar historial de mantenimiento, y más.

---

## Tecnologías utilizadas

- **Backend:** PHP 8.2 + Laravel 11
- **Base de datos:** MySQL (phpMyAdmin)
- **Frontend:** Blade Templates + CSS personalizado
- **Autenticación:** Laravel Auth con roles personalizados + recuperación de contraseña vía email

---

## Módulos implementados

| Módulo | Descripción | Roles con acceso |
|---|---|---|
| Autenticación | Login por correo o nombre de usuario, recuperación de contraseña | Todos |
| Dashboard | Vista personalizada por rol tras el login | Todos |
| Usuarios | CRUD de personal con acceso al sistema, generación automática de usuario | Admin |
| Bitácora | Registro de inicios/cierres de sesión y gestión de usuarios | Admin |
| Cargos | Asignación de tipos de trabajo al personal (tabla `adquiere`) | Admin |
| Clientes | Registro y edición de clientes (sin eliminación) | Admin, Recepcionista |
| Vehículos | Ficha técnica de vehículos, eliminación protegida si tiene diagnósticos | Admin, Recepcionista |
| Historial | Consulta de diagnósticos y órdenes de trabajo por vehículo | Todos |

> Algunos módulos (proformas, órdenes de trabajo, herramientas) están estructurados en la BD y pendientes de implementar en la interfaz.

---

## Roles del sistema

| ID | Rol | Descripción |
|---|---|---|
| 1 | Administrador | Control total — usuarios, bitácora, cargos, clientes, vehículos, historial |
| 2 | Mecánico Jefe | Dashboard propio + consulta de historial |
| 3 | Recepcionista | Clientes, vehículos e historial |

> Los **clientes** no tienen acceso al sistema — solo se registran en la base de datos como personas.

---

## Requisitos previos

- PHP **8.2** o superior
- **Composer**
- **MySQL** (XAMPP, Laragon, o similar)
- Cuenta en **Mailtrap** (para recuperación de contraseña en desarrollo)

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

Edita el `.env` con tus datos:
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
> Las credenciales de Mailtrap se obtienen en mailtrap.io → Email Testing → SMTP Settings → Laravel.

### 4. Migraciones y seeders
```bash
php artisan migrate --seed
```

Crea las 32 tablas y carga datos iniciales: roles, permisos, tipos de trabajador, tipos y marcas de herramienta, y los 3 usuarios de prueba.

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

> Puedes ingresar con el correo **o** con el nombre de usuario — ambos funcionan.

---

## Recuperación de contraseña

El sistema usa **Mailtrap** para pruebas de recuperación de contraseña:

1. Ve a `/login` → *¿Olvidaste tu contraseña?*
2. Ingresa el correo del usuario
3. Abre tu bandeja en mailtrap.io y copia el enlace
4. Ingresa y confirma la nueva contraseña

---

## Estructura del proyecto

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/                  ← Login, logout, reset password
│   │   ├── DashboardController    ← Redirección por rol
│   │   ├── UsuarioController      ← CRUD usuarios
│   │   ├── ClienteController      ← CRUD clientes
│   │   ├── AutoController         ← CRUD vehículos
│   │   ├── HistorialController    ← Historial de mantenimiento
│   │   ├── CargoController        ← Asignación de tipos de trabajo
│   │   └── BitacoraController     ← Registro de actividad
│   ├── Middleware/CheckRol.php    ← Control de acceso por rol
│   └── Requests/Auth/LoginRequest ← Login por correo o usuario
├── Models/                        ← Un modelo por tabla
database/
├── migrations/                    ← 32 tablas en orden de FK
└── seeders/                       ← Datos iniciales del sistema
resources/views/
├── layouts/app.blade.php          ← Layout con sidebar dinámico por rol
├── auth/                          ← Login, forgot-password, reset-password
├── dashboard/                     ← admin, mecanico, recepcionista
├── usuarios/                      ← CRUD + gestión de cargos
├── clientes/                      ← CRUD clientes
├── autos/                         ← CRUD vehículos
├── historial/                     ← Búsqueda e historial por vehículo
└── bitacora/                      ← Tabla de auditoría
routes/
├── web.php                        ← Rutas protegidas por rol
└── auth.php                       ← Rutas de autenticación (Breeze)
```

---

## Comandos útiles

```bash
# Reiniciar BD y resembrar desde cero
php artisan migrate:fresh --seed

# Seeders individuales
php artisan db:seed --class=AdminSeeder
php artisan db:seed --class=RolSeeder

# Ver todas las rutas
php artisan route:list

# Limpiar caché (ejecutar tras cambios en .env o config)
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Vaciar sesiones activas
php artisan session:flush
```

---

## Notas importantes

- El `.env` **nunca** se sube al repositorio — cada desarrollador crea el suyo desde `.env.example`.
- Las contraseñas del seeder son solo para **desarrollo** — cambiarlas antes de producción.
- Para despliegue en la nube se recomienda mantener `SESSION_DRIVER=database` y configurar un SMTP real en lugar de Mailtrap.