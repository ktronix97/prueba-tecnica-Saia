# Proyecto: Gestión de Tareas y Reportes

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

## 📋 Descripción

Este proyecto implementa una API RESTful en Symfony 7.2 con autenticación JWT, gestión de usuarios, tareas, categorías y reportes. El frontend híbrido combina Vue 3 (Composition API) y jQuery para una experiencia dinámica y accesible.

---

## 🚀 Instalación y ejecución

### 1️⃣ Clonar el repositorio

```bash
git clone https://github.com/usuario/proyecto.git
cd proyecto
2️⃣ Instalar dependencias
Symfony (backend)
bash
composer install
composer create-project symfony/skeleton nombre-del-proyecto

Esto crea un proyecto mínimo de Symfony. Luego puedes añadir los paquetes necesarios:
bash
composer require webapp

Frontend (Vue 3 + jQuery)
bash
npm install

3️⃣ Configurar entorno
Copia el archivo .env y personaliza tus credenciales:

bash
cp .env .env.local
Edita .env.local:

dotenv
DATABASE_URL="mysql://usuario:clave@127.0.0.1:3306/nombre_bd?serverVersion=8.0.32"
MAILER_DSN=smtp://usuario:clave@smtp.mailtrap.io:2525


5️⃣ Crear usuario administrador por defecto
bash
php bin/console app:crear-admin
Este comando crea:

Email: admin@demo.com

Contraseña: admin123

Rol: ROLE_ADMIN

6️⃣ Compilar frontend
bash
npm run build
Para desarrollo: npm run dev

7️⃣ Ejecutar servidor Symfony CLI
bash
symfony serve

O con PHP:

bash
php -S localhost:8000 -t public

🔐 Acceso al sistema

Endpoint: POST /api/auth/login

Email: admin@demo.com
Contraseña: admin123
Autenticación: JWT

📦 Dependencias clave
Symfony

symfony/orm-pack
symfony/security-bundle
lexik/jwt-authentication-bundle
symfony/maker-bundle
symfony/validator
symfony/serializer

Frontend

vue@3
jquery
vite


🧠 Notas adicionales
El proyecto usa access_control para proteger rutas según roles.

El login y registro están protegidos por JWT.

Se recomienda usar Postman o Insomnia para probar los endpoints.

4. Configuración de JWT
En tu archivo .env añade:

dotenv
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=tu_passphrase_secreta

Genera o regenera las claves:
bash
    php bin/console lexik:jwt:generate-keypair --overwrite

5. Endpoints Principales
5.1 Autenticación
POST /api/auth/login

json
{ "email": "...", "password": "..." }
POST /api/token/refresh

json
{ "refresh_token": "..." }
5.2 Usuarios
GET /api/usuarios

POST /api/usuarios

PUT /api/usuarios/{id}

DELETE /api/usuarios/{id}

5.3 Tareas

GET /api/tareas

GET /api/tareas/{id}

POST /api/tareas

PUT /api/tareas/{id}

DELETE /api/tareas/{id}

En listados puedes enviar parámetros de consulta:

Código
?estado=pendiente&prioridad=alta&usuario=3&orden=fechaVencimiento&direccion=DESC
6. Arquitectura y Código Relevante
6.1 Repositorio de Tareas con filtros
php
public function buscarTareas(array $c): array
{
    $qb = $this->createQueryBuilder('t');

    if (!empty($c['estado'])) {
        $qb->andWhere('t.estado = :estado')
           ->setParameter('estado', $c['estado']);
    }

    // …otros filtros…

    if (!empty($c['orden'])) {
        $qb->orderBy('t.' . $c['orden'], $c['direccion'] ?? 'ASC');
    }

    return $qb->getQuery()->getResult();
}

6.2 Comando de reporte diario
#[AsCommand(
    name: 'app:reporte:diario',
    description: 'Genera y persiste los reportes diarios de tareas.'
)]
class GenerarReporteDiarioCommand extends Command
{
    use LockableTrait;

    public function __construct(private ReporteService $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('date', InputArgument::OPTIONAL, 'Fecha a procesar (YYYY-MM-DD)', date('Y-m-d'));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (false === $this->lock()) {
            $output->writeln('El comando ya está en ejecución. Abortando.');
            return Command::FAILURE;
        }

        $date = $input->getArgument('date');
        $reportes = $this->service->generarReporteDiario(new \DateTimeImmutable($date));
        $count = count($reportes);

        $output->writeln("[$date] Se generaron {$count} reportes diarios.");
        $this->release();

        return Command::SUCCESS;
    }
}

7. Optimización y Performance

7.1 Índices aplicados
sql
CREATE INDEX idx_estado       ON tarea(estado);
CREATE INDEX idx_prioridad    ON tarea(prioridad);
CREATE INDEX idx_vencimiento  ON tarea(fecha_vencimiento);
CREATE INDEX idx_asignado     ON tarea(asignado_a_id);

7.2 EXPLAIN de consulta principal
sql
EXPLAIN SELECT * FROM tarea
WHERE estado = 'pendiente'
  AND prioridad = 'alta'
ORDER BY fecha_vencimiento DESC;
id	select_type	table	type	key	rows	Extra
1	SIMPLE	tarea	ref	idx_estado	10	Using where

8. Exportación de Reportes
CSV: usa fputcsv o League\Csv.

PDF: con KnpSnappyBundle o Dompdf.

php
public function generarCSV(array $t, string $ruta) {
    $fp = fopen($ruta, 'w');
    fputcsv($fp, ['Título','Estado','Prioridad','Usuario','Vencimiento']);

    foreach ($t as $tarea) {
        fputcsv($fp, [
            $tarea->getTitulo(),
            $tarea->getEstado(),
            $tarea->getPrioridad(),
            $tarea->getAsignadoA()->getEmail(),
            $tarea->getFechaVencimiento()->format('Y-m-d'),
        ]);
    }

    fclose($fp);