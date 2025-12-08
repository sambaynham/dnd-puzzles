<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251208105051 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE casebook_subject_clue ADD type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE casebook_subject_clue ADD CONSTRAINT FK_EFB778DDC54C8C93 FOREIGN KEY (type_id) REFERENCES casebook_subject_clue_type (id)');
        $this->addSql('CREATE INDEX IDX_EFB778DDC54C8C93 ON casebook_subject_clue (type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE casebook_subject_clue DROP FOREIGN KEY FK_EFB778DDC54C8C93');
        $this->addSql('DROP INDEX IDX_EFB778DDC54C8C93 ON casebook_subject_clue');
        $this->addSql('ALTER TABLE casebook_subject_clue DROP type_id');
    }
}
