<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260517171642 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create article table for the News module plus its dh_auditor audit table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE article (title VARCHAR(200) NOT NULL, slug VARCHAR(220) NOT NULL, excerpt LONGTEXT DEFAULT NULL, cover_path VARCHAR(255) DEFAULT NULL, content JSON NOT NULL, published_at DATETIME DEFAULT NULL, id BINARY(16) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, author_id BINARY(16) DEFAULT NULL, UNIQUE INDEX UNIQ_23A0E66989D9B62 (slug), INDEX IDX_23A0E66F675F31B (author_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66F675F31B FOREIGN KEY (author_id) REFERENCES `user` (id) ON DELETE SET NULL');

        $this->addSql(<<<'SQL'
            CREATE TABLE article_audit (
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
                INDEX type_c17ec30d772af1f2052f191e1bcad73b_idx (type),
                INDEX object_id_c17ec30d772af1f2052f191e1bcad73b_idx (object_id),
                INDEX discriminator_c17ec30d772af1f2052f191e1bcad73b_idx (discriminator),
                INDEX transaction_hash_c17ec30d772af1f2052f191e1bcad73b_idx (transaction_hash),
                INDEX blame_id_c17ec30d772af1f2052f191e1bcad73b_idx (blame_id),
                INDEX created_at_c17ec30d772af1f2052f191e1bcad73b_idx (created_at),
                PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE article_audit');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66F675F31B');
        $this->addSql('DROP TABLE article');
    }
}
