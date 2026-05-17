<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260517155415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename rider.photo_url and staff.photo_url to photo_path (Flysystem-managed)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE rider CHANGE photo_url photo_path VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE staff CHANGE photo_url photo_path VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE rider CHANGE photo_path photo_url VARCHAR(500) DEFAULT NULL');
        $this->addSql('ALTER TABLE staff CHANGE photo_path photo_url VARCHAR(500) DEFAULT NULL');
    }
}
