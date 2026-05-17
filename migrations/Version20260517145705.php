<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260517145705 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create rider and staff tables for the Team module';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rider (first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, date_of_birth DATE NOT NULL, category VARCHAR(30) NOT NULL, bib_number INT DEFAULT NULL, photo_url VARCHAR(500) DEFAULT NULL, bio LONGTEXT DEFAULT NULL, palmares LONGTEXT DEFAULT NULL, active TINYINT NOT NULL, id BINARY(16) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE staff (first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, role VARCHAR(30) NOT NULL, photo_url VARCHAR(500) DEFAULT NULL, bio LONGTEXT DEFAULT NULL, active TINYINT NOT NULL, id BINARY(16) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE rider');
        $this->addSql('DROP TABLE staff');
    }
}
