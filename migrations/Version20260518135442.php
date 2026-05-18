<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260518135442 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create result + result_audit tables for the Uitslagen module.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE result (race_name VARCHAR(200) DEFAULT NULL, race_date DATE DEFAULT NULL, race_location VARCHAR(150) DEFAULT NULL, discipline VARCHAR(16) DEFAULT NULL, status VARCHAR(16) NOT NULL, rank INT DEFAULT NULL, notes LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, id BINARY(16) NOT NULL, rider_id BINARY(16) NOT NULL, race_id BINARY(16) DEFAULT NULL, INDEX IDX_136AC113FF881F6 (rider_id), INDEX IDX_136AC1136E59D40D (race_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE result_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs JSON DEFAULT NULL, extra_data JSON DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL, INDEX type_319cc075178d1b832b45a55df345c5be_idx (type), INDEX object_id_319cc075178d1b832b45a55df345c5be_idx (object_id), INDEX discriminator_319cc075178d1b832b45a55df345c5be_idx (discriminator), INDEX transaction_hash_319cc075178d1b832b45a55df345c5be_idx (transaction_hash), INDEX blame_id_319cc075178d1b832b45a55df345c5be_idx (blame_id), INDEX created_at_319cc075178d1b832b45a55df345c5be_idx (created_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE result ADD CONSTRAINT FK_136AC113FF881F6 FOREIGN KEY (rider_id) REFERENCES rider (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE result ADD CONSTRAINT FK_136AC1136E59D40D FOREIGN KEY (race_id) REFERENCES race (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE rider_audit RENAME INDEX rider_audit_type_idx TO type_e177392b919be8cbc877929153d7fbf7_idx');
        $this->addSql('ALTER TABLE rider_audit RENAME INDEX rider_audit_object_id_idx TO object_id_e177392b919be8cbc877929153d7fbf7_idx');
        $this->addSql('ALTER TABLE rider_audit RENAME INDEX rider_audit_discriminator_idx TO discriminator_e177392b919be8cbc877929153d7fbf7_idx');
        $this->addSql('ALTER TABLE rider_audit RENAME INDEX rider_audit_transaction_hash_idx TO transaction_hash_e177392b919be8cbc877929153d7fbf7_idx');
        $this->addSql('ALTER TABLE rider_audit RENAME INDEX rider_audit_blame_id_idx TO blame_id_e177392b919be8cbc877929153d7fbf7_idx');
        $this->addSql('ALTER TABLE rider_audit RENAME INDEX rider_audit_created_at_idx TO created_at_e177392b919be8cbc877929153d7fbf7_idx');
        $this->addSql('ALTER TABLE sponsor_audit RENAME INDEX sponsor_audit_type_idx TO type_4c0690f47c541b2c360b26e4ea8ba68d_idx');
        $this->addSql('ALTER TABLE sponsor_audit RENAME INDEX sponsor_audit_object_id_idx TO object_id_4c0690f47c541b2c360b26e4ea8ba68d_idx');
        $this->addSql('ALTER TABLE sponsor_audit RENAME INDEX sponsor_audit_discriminator_idx TO discriminator_4c0690f47c541b2c360b26e4ea8ba68d_idx');
        $this->addSql('ALTER TABLE sponsor_audit RENAME INDEX sponsor_audit_transaction_hash_idx TO transaction_hash_4c0690f47c541b2c360b26e4ea8ba68d_idx');
        $this->addSql('ALTER TABLE sponsor_audit RENAME INDEX sponsor_audit_blame_id_idx TO blame_id_4c0690f47c541b2c360b26e4ea8ba68d_idx');
        $this->addSql('ALTER TABLE sponsor_audit RENAME INDEX sponsor_audit_created_at_idx TO created_at_4c0690f47c541b2c360b26e4ea8ba68d_idx');
        $this->addSql('ALTER TABLE staff_audit RENAME INDEX staff_audit_type_idx TO type_ac155fbf7289c1c26ad8ee07ca42ba82_idx');
        $this->addSql('ALTER TABLE staff_audit RENAME INDEX staff_audit_object_id_idx TO object_id_ac155fbf7289c1c26ad8ee07ca42ba82_idx');
        $this->addSql('ALTER TABLE staff_audit RENAME INDEX staff_audit_discriminator_idx TO discriminator_ac155fbf7289c1c26ad8ee07ca42ba82_idx');
        $this->addSql('ALTER TABLE staff_audit RENAME INDEX staff_audit_transaction_hash_idx TO transaction_hash_ac155fbf7289c1c26ad8ee07ca42ba82_idx');
        $this->addSql('ALTER TABLE staff_audit RENAME INDEX staff_audit_blame_id_idx TO blame_id_ac155fbf7289c1c26ad8ee07ca42ba82_idx');
        $this->addSql('ALTER TABLE staff_audit RENAME INDEX staff_audit_created_at_idx TO created_at_ac155fbf7289c1c26ad8ee07ca42ba82_idx');
        $this->addSql('ALTER TABLE user_audit RENAME INDEX user_audit_type_idx TO type_e06395edc291d0719bee26fd39a32e8a_idx');
        $this->addSql('ALTER TABLE user_audit RENAME INDEX user_audit_object_id_idx TO object_id_e06395edc291d0719bee26fd39a32e8a_idx');
        $this->addSql('ALTER TABLE user_audit RENAME INDEX user_audit_discriminator_idx TO discriminator_e06395edc291d0719bee26fd39a32e8a_idx');
        $this->addSql('ALTER TABLE user_audit RENAME INDEX user_audit_transaction_hash_idx TO transaction_hash_e06395edc291d0719bee26fd39a32e8a_idx');
        $this->addSql('ALTER TABLE user_audit RENAME INDEX user_audit_blame_id_idx TO blame_id_e06395edc291d0719bee26fd39a32e8a_idx');
        $this->addSql('ALTER TABLE user_audit RENAME INDEX user_audit_created_at_idx TO created_at_e06395edc291d0719bee26fd39a32e8a_idx');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE result DROP FOREIGN KEY FK_136AC113FF881F6');
        $this->addSql('ALTER TABLE result DROP FOREIGN KEY FK_136AC1136E59D40D');
        $this->addSql('DROP TABLE result');
        $this->addSql('DROP TABLE result_audit');
        $this->addSql('ALTER TABLE rider_audit RENAME INDEX created_at_e177392b919be8cbc877929153d7fbf7_idx TO rider_audit_created_at_idx');
        $this->addSql('ALTER TABLE rider_audit RENAME INDEX discriminator_e177392b919be8cbc877929153d7fbf7_idx TO rider_audit_discriminator_idx');
        $this->addSql('ALTER TABLE rider_audit RENAME INDEX transaction_hash_e177392b919be8cbc877929153d7fbf7_idx TO rider_audit_transaction_hash_idx');
        $this->addSql('ALTER TABLE rider_audit RENAME INDEX type_e177392b919be8cbc877929153d7fbf7_idx TO rider_audit_type_idx');
        $this->addSql('ALTER TABLE rider_audit RENAME INDEX blame_id_e177392b919be8cbc877929153d7fbf7_idx TO rider_audit_blame_id_idx');
        $this->addSql('ALTER TABLE rider_audit RENAME INDEX object_id_e177392b919be8cbc877929153d7fbf7_idx TO rider_audit_object_id_idx');
        $this->addSql('ALTER TABLE sponsor_audit RENAME INDEX created_at_4c0690f47c541b2c360b26e4ea8ba68d_idx TO sponsor_audit_created_at_idx');
        $this->addSql('ALTER TABLE sponsor_audit RENAME INDEX discriminator_4c0690f47c541b2c360b26e4ea8ba68d_idx TO sponsor_audit_discriminator_idx');
        $this->addSql('ALTER TABLE sponsor_audit RENAME INDEX transaction_hash_4c0690f47c541b2c360b26e4ea8ba68d_idx TO sponsor_audit_transaction_hash_idx');
        $this->addSql('ALTER TABLE sponsor_audit RENAME INDEX type_4c0690f47c541b2c360b26e4ea8ba68d_idx TO sponsor_audit_type_idx');
        $this->addSql('ALTER TABLE sponsor_audit RENAME INDEX blame_id_4c0690f47c541b2c360b26e4ea8ba68d_idx TO sponsor_audit_blame_id_idx');
        $this->addSql('ALTER TABLE sponsor_audit RENAME INDEX object_id_4c0690f47c541b2c360b26e4ea8ba68d_idx TO sponsor_audit_object_id_idx');
        $this->addSql('ALTER TABLE staff_audit RENAME INDEX transaction_hash_ac155fbf7289c1c26ad8ee07ca42ba82_idx TO staff_audit_transaction_hash_idx');
        $this->addSql('ALTER TABLE staff_audit RENAME INDEX type_ac155fbf7289c1c26ad8ee07ca42ba82_idx TO staff_audit_type_idx');
        $this->addSql('ALTER TABLE staff_audit RENAME INDEX blame_id_ac155fbf7289c1c26ad8ee07ca42ba82_idx TO staff_audit_blame_id_idx');
        $this->addSql('ALTER TABLE staff_audit RENAME INDEX object_id_ac155fbf7289c1c26ad8ee07ca42ba82_idx TO staff_audit_object_id_idx');
        $this->addSql('ALTER TABLE staff_audit RENAME INDEX created_at_ac155fbf7289c1c26ad8ee07ca42ba82_idx TO staff_audit_created_at_idx');
        $this->addSql('ALTER TABLE staff_audit RENAME INDEX discriminator_ac155fbf7289c1c26ad8ee07ca42ba82_idx TO staff_audit_discriminator_idx');
        $this->addSql('ALTER TABLE user_audit RENAME INDEX created_at_e06395edc291d0719bee26fd39a32e8a_idx TO user_audit_created_at_idx');
        $this->addSql('ALTER TABLE user_audit RENAME INDEX discriminator_e06395edc291d0719bee26fd39a32e8a_idx TO user_audit_discriminator_idx');
        $this->addSql('ALTER TABLE user_audit RENAME INDEX transaction_hash_e06395edc291d0719bee26fd39a32e8a_idx TO user_audit_transaction_hash_idx');
        $this->addSql('ALTER TABLE user_audit RENAME INDEX type_e06395edc291d0719bee26fd39a32e8a_idx TO user_audit_type_idx');
        $this->addSql('ALTER TABLE user_audit RENAME INDEX blame_id_e06395edc291d0719bee26fd39a32e8a_idx TO user_audit_blame_id_idx');
        $this->addSql('ALTER TABLE user_audit RENAME INDEX object_id_e06395edc291d0719bee26fd39a32e8a_idx TO user_audit_object_id_idx');
    }
}
