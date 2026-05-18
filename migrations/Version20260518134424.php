<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260518134424 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create race + race_audit tables for the Programme module.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE race (name VARCHAR(200) NOT NULL, starts_at DATETIME NOT NULL, location VARCHAR(150) NOT NULL, discipline VARCHAR(16) NOT NULL, categories JSON NOT NULL, description LONGTEXT DEFAULT NULL, external_url VARCHAR(500) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, id BINARY(16) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE race_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs JSON DEFAULT NULL, extra_data JSON DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL, INDEX type_deb4aff7a6a7f086ed1213cafc85c4a4_idx (type), INDEX object_id_deb4aff7a6a7f086ed1213cafc85c4a4_idx (object_id), INDEX discriminator_deb4aff7a6a7f086ed1213cafc85c4a4_idx (discriminator), INDEX transaction_hash_deb4aff7a6a7f086ed1213cafc85c4a4_idx (transaction_hash), INDEX blame_id_deb4aff7a6a7f086ed1213cafc85c4a4_idx (blame_id), INDEX created_at_deb4aff7a6a7f086ed1213cafc85c4a4_idx (created_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE race');
        $this->addSql('DROP TABLE race_audit');
    }
}
