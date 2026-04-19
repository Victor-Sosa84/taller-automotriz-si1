# Sistema de Gestión — Taller Mecánico Automotriz

Sistema web desarrollado con **Laravel 11** para la gestión interna de un taller automotriz. Permite administrar usuarios del personal, registrar clientes, gestionar órdenes de trabajo, proformas, facturación, herramientas y más.

---

## Tecnologías utilizadas

- **Backend:** PHP 8.2 + Laravel 11
- **Base de datos:** MySQL (phpMyAdmin)
- **Frontend:** Blade Templates + CSS personalizado
- **Autenticación:** Laravel Auth con roles personalizados

---

## Roles del sistema

El sistema tiene **3 roles**, cada uno con su propio dashboard y permisos:

| ID | Rol | Descripción |
|---|---|---|
| 1 | Administrador | Control total — usuarios, bitácora, configuración |
| 2 | Mecánico Jefe | Gestión de órdenes de trabajo y herramientas |
| 3 | Recepcionista | Atención al cliente, proformas e ingresos |

> El registro de **clientes** es independiente al sistema de roles — los clientes no tienen acceso a la plataforma, solo se registran en la base de datos.

---

## Requisitos previos

Antes de instalar, verifica que tienes:

- PHP **8.3** o superior
- **Composer**
- **MySQL** (XAMPP, Laragon, o similar)
- **Node.js** (solo si vas a compilar assets)

Verifica con:
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

### 2. Instalar dependencias PHP
```bash
composer install
```

### 3. Configurar el entorno
```bash
cp .env.example .env
php artisan key:generate
```

Abre el archivo `.env` y configura tu base de datos:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_taller_si1
DB_USERNAME=root
DB_PASSWORD=
```

> Crea la base de datos `db_taller_si1` en phpMyAdmin antes de continuar.

### 4. Ejecutar migraciones y seeders
```bash
php artisan migrate --seed
```

Esto crea todas las tablas y carga los datos iniciales (roles, permisos, tipos y usuarios de prueba).

### 5. Levantar el servidor
```bash
php artisan serve
```

Abre tu navegador en **http://localhost:8000**

---

## Usuarios de prueba

Una vez ejecutado el seeder, puedes ingresar con cualquiera de estos usuarios:

| Rol | Correo | Contraseña |
|---|---|---|
| Administrador | `admin@taller.com` | `Admin1234!` |
| Mecánico Jefe | `mecanico@taller.com` | `Mecanico1234!` |
| Recepcionista | `recepcion@taller.com` | `Recepcion1234!` |

> También puedes ingresar con el **nombre de usuario** generado automáticamente en lugar del correo (ej. `aprincipal`, `mprueba`, `rprueba`).

---

## Estructura del proyecto

```
app/
├── Http/
│   ├── Controllers/          ← Lógica de cada módulo
│   ├── Middleware/CheckRol.php  ← Control de acceso por rol
│   └── Requests/Auth/        ← Validación del login
├── Models/                   ← Modelos Eloquent (una por tabla)
database/
├── migrations/               ← Estructura de las 32 tablas
└── seeders/                  ← Datos iniciales del sistema
resources/views/
├── layouts/app.blade.php     ← Layout con sidebar dinámico
├── auth/login.blade.php      ← Pantalla de inicio de sesión
├── dashboard/                ← Vista por rol
├── usuarios/                 ← CRUD de usuarios
└── bitacora/                 ← Registro de actividad
routes/
└── web.php                   ← Rutas protegidas por rol
```

---

## Comandos útiles durante el desarrollo

```bash
# Reiniciar toda la base de datos y resembrar
php artisan migrate:fresh --seed

# Correr solo un seeder específico
php artisan db:seed --class=UsuarioSeeder

# Ver todas las rutas registradas
php artisan route:list

# Limpiar caché de configuración
php artisan config:clear
php artisan cache:clear
```

---

## Notas importantes

- El archivo `.env` **nunca** se sube al repositorio — contiene credenciales locales.
- Cada desarrollador debe crear su propio `.env` a partir de `.env.example`.
- Las contraseñas de los seeders son solo para **desarrollo** — cambiarlas antes de cualquier despliegue real.
