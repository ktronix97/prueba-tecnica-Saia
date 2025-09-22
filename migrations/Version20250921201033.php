<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250921201033 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE refresh_tokens (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, token_hash VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, revoked TINYINT(1) NOT NULL, INDEX IDX_9BACE7E1A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE refresh_tokens ADD CONSTRAINT FK_9BACE7E1A76ED395 FOREIGN KEY (user_id) REFERENCES usuario (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE categoria ADD descripcion VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE usuario ADD rol VARCHAR(255) NOT NULL, DROP roles');
        $this->addSql('ALTER TABLE usuario ADD CONSTRAINT CHK_USUARIO_ROL CHECK (rol IN (\'ROLE_ADMIN\',\'ROLE_USER\'))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE refresh_tokens DROP FOREIGN KEY FK_9BACE7E1A76ED395');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('ALTER TABLE categoria DROP descripcion');
        $this->addSql('ALTER TABLE usuario ADD roles JSON NOT NULL, DROP rol');
    }
}
