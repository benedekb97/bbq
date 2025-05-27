<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250527122933 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE queue (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, `default` TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE queued_user (id INT AUTO_INCREMENT NOT NULL, queue_id INT DEFAULT NULL, user_id VARCHAR(255) NOT NULL, INDEX IDX_EA70565D477B5BAE (queue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE queued_user ADD CONSTRAINT FK_EA70565D477B5BAE FOREIGN KEY (queue_id) REFERENCES queue (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE queued_user DROP FOREIGN KEY FK_EA70565D477B5BAE
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE queue
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE queued_user
        SQL);
    }
}
