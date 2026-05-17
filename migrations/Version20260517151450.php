<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260517151450 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Drop unused fields from rider (bib_number, bio, palmares) and staff (bio)';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rider DROP bib_number, DROP bio, DROP palmares');
        $this->addSql('ALTER TABLE staff DROP bio');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rider ADD bib_number INT DEFAULT NULL, ADD bio LONGTEXT DEFAULT NULL, ADD palmares LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE staff ADD bio LONGTEXT DEFAULT NULL');
    }
}
