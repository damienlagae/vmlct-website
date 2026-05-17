<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260517154743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename sponsor.logo_url to sponsor.logo_path (now stores a Flysystem path)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sponsor CHANGE logo_url logo_path VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sponsor CHANGE logo_path logo_url VARCHAR(500) DEFAULT NULL');
    }
}
