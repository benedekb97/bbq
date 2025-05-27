<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250527124634 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE queue ADD expiry_in_minutes INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE queued_user ADD expires_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', ADD deleted_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE queue DROP expiry_in_minutes
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE queued_user DROP expires_at, DROP deleted_at
        SQL);
    }
}
