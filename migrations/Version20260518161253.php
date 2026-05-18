<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Creates race + race_stage + result (and their audit twins).
 *
 * Schema highlights:
 *  - race: day-precision dates (start_date / nullable end_date)
 *  - race_stage: optional name + stage_date, position 1-indexed
 *  - result: linked to a stage (not a race directly), with an
 *    integer rank; no status/DNF column.
 */
final class Version20260518161253 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create race + race_stage + result tables (Programme + Uitslagen).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE race (name VARCHAR(200) NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, location VARCHAR(150) NOT NULL, discipline VARCHAR(16) NOT NULL, categories JSON NOT NULL, description LONGTEXT DEFAULT NULL, external_url VARCHAR(500) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, id BINARY(16) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE race_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs JSON DEFAULT NULL, extra_data JSON DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL, INDEX type_deb4aff7a6a7f086ed1213cafc85c4a4_idx (type), INDEX object_id_deb4aff7a6a7f086ed1213cafc85c4a4_idx (object_id), INDEX discriminator_deb4aff7a6a7f086ed1213cafc85c4a4_idx (discriminator), INDEX transaction_hash_deb4aff7a6a7f086ed1213cafc85c4a4_idx (transaction_hash), INDEX blame_id_deb4aff7a6a7f086ed1213cafc85c4a4_idx (blame_id), INDEX created_at_deb4aff7a6a7f086ed1213cafc85c4a4_idx (created_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE race_stage (name VARCHAR(200) DEFAULT NULL, stage_date DATE DEFAULT NULL, position SMALLINT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, id BINARY(16) NOT NULL, race_id BINARY(16) NOT NULL, INDEX IDX_2442B2786E59D40D (race_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE race_stage_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs JSON DEFAULT NULL, extra_data JSON DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL, INDEX type_763634b01c8e9f6cadd607d39fd98455_idx (type), INDEX object_id_763634b01c8e9f6cadd607d39fd98455_idx (object_id), INDEX discriminator_763634b01c8e9f6cadd607d39fd98455_idx (discriminator), INDEX transaction_hash_763634b01c8e9f6cadd607d39fd98455_idx (transaction_hash), INDEX blame_id_763634b01c8e9f6cadd607d39fd98455_idx (blame_id), INDEX created_at_763634b01c8e9f6cadd607d39fd98455_idx (created_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE result (rank INT NOT NULL, notes LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, id BINARY(16) NOT NULL, rider_id BINARY(16) NOT NULL, stage_id BINARY(16) NOT NULL, INDEX IDX_136AC113FF881F6 (rider_id), INDEX IDX_136AC1132298D193 (stage_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE result_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs JSON DEFAULT NULL, extra_data JSON DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL, INDEX type_319cc075178d1b832b45a55df345c5be_idx (type), INDEX object_id_319cc075178d1b832b45a55df345c5be_idx (object_id), INDEX discriminator_319cc075178d1b832b45a55df345c5be_idx (discriminator), INDEX transaction_hash_319cc075178d1b832b45a55df345c5be_idx (transaction_hash), INDEX blame_id_319cc075178d1b832b45a55df345c5be_idx (blame_id), INDEX created_at_319cc075178d1b832b45a55df345c5be_idx (created_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE race_stage ADD CONSTRAINT FK_2442B2786E59D40D FOREIGN KEY (race_id) REFERENCES race (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE result ADD CONSTRAINT FK_136AC113FF881F6 FOREIGN KEY (rider_id) REFERENCES rider (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE result ADD CONSTRAINT FK_136AC1132298D193 FOREIGN KEY (stage_id) REFERENCES race_stage (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE result DROP FOREIGN KEY FK_136AC113FF881F6');
        $this->addSql('ALTER TABLE result DROP FOREIGN KEY FK_136AC1132298D193');
        $this->addSql('ALTER TABLE race_stage DROP FOREIGN KEY FK_2442B2786E59D40D');
        $this->addSql('DROP TABLE result');
        $this->addSql('DROP TABLE result_audit');
        $this->addSql('DROP TABLE race_stage');
        $this->addSql('DROP TABLE race_stage_audit');
        $this->addSql('DROP TABLE race');
        $this->addSql('DROP TABLE race_audit');
    }
}
