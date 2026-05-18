<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260518081042 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create page + page_audit tables for the dynamic Page module.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE page (title VARCHAR(200) NOT NULL, path VARCHAR(220) NOT NULL, excerpt LONGTEXT DEFAULT NULL, content JSON NOT NULL, published_at DATETIME DEFAULT NULL, meta_title VARCHAR(200) DEFAULT NULL, meta_description LONGTEXT DEFAULT NULL, id BINARY(16) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_140AB620B548B0F (path), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE page_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs JSON DEFAULT NULL, extra_data JSON DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL, INDEX type_cb0a2a12940e3e1281c35c60e8ceadcd_idx (type), INDEX object_id_cb0a2a12940e3e1281c35c60e8ceadcd_idx (object_id), INDEX discriminator_cb0a2a12940e3e1281c35c60e8ceadcd_idx (discriminator), INDEX transaction_hash_cb0a2a12940e3e1281c35c60e8ceadcd_idx (transaction_hash), INDEX blame_id_cb0a2a12940e3e1281c35c60e8ceadcd_idx (blame_id), INDEX created_at_cb0a2a12940e3e1281c35c60e8ceadcd_idx (created_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE page');
        $this->addSql('DROP TABLE page_audit');
    }
}
