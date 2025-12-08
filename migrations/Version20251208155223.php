<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251208155223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE puzzle_instance ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD name VARCHAR(255) NOT NULL, ADD description VARCHAR(2048) NOT NULL, CHANGE publication_date publication_date VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_166C5C8F5F1F6053 ON puzzle_instance (instance_code)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_166C5C8F5F1F6053 ON puzzle_instance');
        $this->addSql('ALTER TABLE puzzle_instance DROP created_at, DROP updated_at, DROP name, DROP description, CHANGE publication_date publication_date DATETIME DEFAULT NULL');
    }
}
