<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260517190000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add audit tables for sponsor, rider, staff and user (dh_auditor)';
    }

    public function up(Schema $schema): void
    {
        foreach (['sponsor', 'rider', 'staff', 'user'] as $entity) {
            $this->addSql(\sprintf(<<<'SQL'
                CREATE TABLE %s_audit (
                    id INT UNSIGNED AUTO_INCREMENT NOT NULL,
                    type VARCHAR(10) NOT NULL,
                    object_id VARCHAR(255) NOT NULL,
                    discriminator VARCHAR(255) DEFAULT NULL,
                    transaction_hash VARCHAR(40) DEFAULT NULL,
                    diffs JSON DEFAULT NULL,
                    extra_data JSON DEFAULT NULL,
                    blame_id VARCHAR(255) DEFAULT NULL,
                    blame_user VARCHAR(255) DEFAULT NULL,
                    blame_user_fqdn VARCHAR(255) DEFAULT NULL,
                    blame_user_firewall VARCHAR(100) DEFAULT NULL,
                    ip VARCHAR(45) DEFAULT NULL,
                    created_at DATETIME NOT NULL,
                    INDEX %s_audit_type_idx (type),
                    INDEX %s_audit_object_id_idx (object_id),
                    INDEX %s_audit_discriminator_idx (discriminator),
                    INDEX %s_audit_transaction_hash_idx (transaction_hash),
                    INDEX %s_audit_blame_id_idx (blame_id),
                    INDEX %s_audit_created_at_idx (created_at),
                    PRIMARY KEY (id)
                ) DEFAULT CHARACTER SET utf8mb4
                SQL, $entity, $entity, $entity, $entity, $entity, $entity, $entity));
        }
    }

    public function down(Schema $schema): void
    {
        foreach (['sponsor', 'rider', 'staff', 'user'] as $entity) {
            $this->addSql(\sprintf('DROP TABLE %s_audit', $entity));
        }
    }
}
