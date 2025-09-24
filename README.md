# 🚀 Proyecto: Gestión de Tareas y Reportes

Una aplicación Symfony que permite gestionar tareas con filtros avanzados, autenticación JWT y generación de reportes automáticos en CSV/PDF.

---

## 1. Descripción

Esta API ofrece:  
- CRUD completo de usuarios y tareas  
- Autenticación JWT con roles  
- Búsqueda, filtrado y ordenamiento dinámico de tareas  
- Comando Symfony para generar reportes diarios  
- Exportación a CSV y PDF  

---

## 2. Requisitos

- PHP 8.3.25  
- Symfony 7.2  
- MySQL (o MariaDB)  
- Composer  
- OpenSSL (para claves JWT)  
- Node.js + Vite

---

## 3. Instalación

Clona el repositorio y entra al directorio:
```bash
git clone https://github.com/tu-usuario/gestion-tareas.git
cd gestion-tareas
Instala dependencias:

bash
composer install
npm install    # si incluyes frontend
Crea la base de datos y ejecuta migraciones:

bash
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate

# 🧪 Prueba Técnica - Proyecto Symfony + Frontend híbrido

🚀 Gestión de Tareas y Reportes
Aplicación Symfony 7.2 con autenticación JWT, gestión de usuarios y tareas, filtros avanzados, y generación de reportes automáticos en CSV/PDF. El frontend híbrido usa Vue 3 (Composition API) y Vite.

✅ Requisitos
PHP 8.3.25

Symfony 7.2

MySQL o MariaDB

Composer



📦 Instalación

1️⃣ Clonar el repositorio
bash
git clone https://github.com/tu-usuario/gestion-tareas.git
cd gestion-tareas
2️⃣ Instalar dependencias

bash
composer install
vite install
⚙️ Configuración del entorno

3️⃣ Variables de entorno
bash
cp .env .env.local
Edita .env.local:

dotenv
DATABASE_URL="mysql://usuario:clave@127.0.0.1:3306/nombre_bd?serverVersion=8.0.32"
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=tu_passphrase_secreta

4️⃣ Generar claves JWT
bash
php bin/console lexik:jwt:generate-keypair --overwrite

5️⃣ Crear base de datos y ejecutar migraciones
bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

🖥️ Ejecución

6️⃣ Compilar frontend con Vite
bash

vite build
# Para desarrollo:
vite dev

7️⃣ Iniciar servidor Symfony

bash
symfony serve
# o
php -S localhost:8000 -t public

🔐 Acceso al sistema
Usuario administrador

bash
Email: admin@demo.com
Contraseña: admin123
Rol: ROLE_ADMIN

Endpoint de login
http

POST /api/auth/login
json
{
  "email": "admin@demo.com",
  "password": "admin123"
}
🔁 Endpoints principales
Autenticación

POST /api/auth/login
POST /api/auth/refresh-token

Usuarios
GET /api/usuarios

POST /api/usuarios

PUT /api/usuarios/{id}

DELETE /api/usuarios/{id}

Tareas
GET /api/tareas

GET /api/tareas/{id}

POST /api/tareas

PUT /api/tareas/{id}

DELETE /api/tareas/{id}

📌 Puedes usar filtros en los listados:

http
GET /api/tareas?estado=pendiente&prioridad=alta&usuario=3&orden=fechaVencimiento&direccion=DESC
🧪 Comando de reporte diario
bash
php bin/console app:reporte:diario
Genera y persiste reportes automáticos por fecha.