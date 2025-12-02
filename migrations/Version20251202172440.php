<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251202172440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE casebook ADD game_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE casebook ADD CONSTRAINT FK_2042C41FE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('CREATE INDEX IDX_2042C41FE48FD905 ON casebook (game_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE casebook DROP FOREIGN KEY FK_2042C41FE48FD905');
        $this->addSql('DROP INDEX IDX_2042C41FE48FD905 ON casebook');
        $this->addSql('ALTER TABLE casebook DROP game_id');
    }
}
