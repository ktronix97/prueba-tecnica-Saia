<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250921005917 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categoria (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_4E10122D3A909126 (nombre), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reporte (id INT AUTO_INCREMENT NOT NULL, tarea_id INT NOT NULL, contenido LONGTEXT NOT NULL, fecha DATETIME NOT NULL, INDEX IDX_5CB12146D5BDFE1 (tarea_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tarea (id INT AUTO_INCREMENT NOT NULL, asignado_a_id INT NOT NULL, titulo VARCHAR(255) NOT NULL, descripcion VARCHAR(255) DEFAULT NULL, fecha_creacion DATETIME NOT NULL, ultima_modificacion DATETIME NOT NULL, estado VARCHAR(20) NOT NULL, prioridad VARCHAR(10) NOT NULL, fecha_vencimiento DATETIME DEFAULT NULL, INDEX IDX_3CA0536639055ADD (asignado_a_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tarea_categoria (tarea_id INT NOT NULL, categoria_id INT NOT NULL, INDEX IDX_5124921E6D5BDFE1 (tarea_id), INDEX IDX_5124921E3397707A (categoria_id), PRIMARY KEY(tarea_id, categoria_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usuario (id INT AUTO_INCREMENT NOT NULL, primer_nombre VARCHAR(50) NOT NULL, segundo_nombre VARCHAR(50) NOT NULL, primer_apellido VARCHAR(50) NOT NULL, segundo_apellido VARCHAR(50) DEFAULT NULL, email VARCHAR(180) NOT NULL, contrasena VARCHAR(255) NOT NULL, roles JSON NOT NULL, UNIQUE INDEX UNIQ_2265B05DE7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reporte ADD CONSTRAINT FK_5CB12146D5BDFE1 FOREIGN KEY (tarea_id) REFERENCES tarea (id)');
        $this->addSql('ALTER TABLE tarea ADD CONSTRAINT FK_3CA0536639055ADD FOREIGN KEY (asignado_a_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE tarea_categoria ADD CONSTRAINT FK_5124921E6D5BDFE1 FOREIGN KEY (tarea_id) REFERENCES tarea (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tarea_categoria ADD CONSTRAINT FK_5124921E3397707A FOREIGN KEY (categoria_id) REFERENCES categoria (id) ON DELETE CASCADE');
        $this->addSql("ALTER TABLE tarea ADD CONSTRAINT CHK_TAREA_ESTADO CHECK (estado IN ('pendiente', 'en progreso', 'completada'))");
        $this->addSql("ALTER TABLE tarea ADD CONSTRAINT CHK_TAREA_PRIORIDAD CHECK (prioridad IN ('baja', 'media', 'alta'))");
        $this->addSql("CREATE INDEX idx_estado       ON tarea(estado)");
        $this->addSql("CREATE INDEX idx_prioridad    ON tarea(prioridad)");
        $this->addSql("CREATE INDEX idx_vencimiento  ON tarea(fecha_vencimiento)");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reporte DROP FOREIGN KEY FK_5CB12146D5BDFE1');
        $this->addSql('ALTER TABLE tarea DROP FOREIGN KEY FK_3CA0536639055ADD');
        $this->addSql('ALTER TABLE tarea_categoria DROP FOREIGN KEY FK_5124921E6D5BDFE1');
        $this->addSql('ALTER TABLE tarea_categoria DROP FOREIGN KEY FK_5124921E3397707A');
        $this->addSql('DROP TABLE categoria');
        $this->addSql('DROP TABLE reporte');
        $this->addSql('DROP TABLE tarea');
        $this->addSql('DROP TABLE tarea_categoria');
        $this->addSql('DROP TABLE usuario');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
