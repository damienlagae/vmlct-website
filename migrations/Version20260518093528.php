<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260518093528 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create menu_item + menu_item_audit tables for the dynamic Menu module.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE menu_item (label VARCHAR(100) NOT NULL, target_type VARCHAR(16) NOT NULL, route_name VARCHAR(100) DEFAULT NULL, position INT NOT NULL, active TINYINT NOT NULL, open_in_new_tab TINYINT NOT NULL, id BINARY(16) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, page_id BINARY(16) DEFAULT NULL, parent_id BINARY(16) DEFAULT NULL, INDEX IDX_D754D550C4663E4 (page_id), INDEX IDX_D754D550727ACA70 (parent_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE menu_item_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs JSON DEFAULT NULL, extra_data JSON DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL, INDEX type_08dd60f4ca6c25595295339c36ae6075_idx (type), INDEX object_id_08dd60f4ca6c25595295339c36ae6075_idx (object_id), INDEX discriminator_08dd60f4ca6c25595295339c36ae6075_idx (discriminator), INDEX transaction_hash_08dd60f4ca6c25595295339c36ae6075_idx (transaction_hash), INDEX blame_id_08dd60f4ca6c25595295339c36ae6075_idx (blame_id), INDEX created_at_08dd60f4ca6c25595295339c36ae6075_idx (created_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE menu_item ADD CONSTRAINT FK_D754D550C4663E4 FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE menu_item ADD CONSTRAINT FK_D754D550727ACA70 FOREIGN KEY (parent_id) REFERENCES menu_item (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu_item DROP FOREIGN KEY FK_D754D550C4663E4');
        $this->addSql('ALTER TABLE menu_item DROP FOREIGN KEY FK_D754D550727ACA70');
        $this->addSql('DROP TABLE menu_item');
        $this->addSql('DROP TABLE menu_item_audit');
    }
}
