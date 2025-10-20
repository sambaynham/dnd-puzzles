<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251020121404 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE casebook (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, brief VARCHAR(2048) NOT NULL, UNIQUE INDEX UNIQ_2042C41F989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE casebook_subject (id INT AUTO_INCREMENT NOT NULL, casebook_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_911EC37B467F13AB (casebook_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE casebook_subject_clue (id INT AUTO_INCREMENT NOT NULL, casebook_subject_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', title VARCHAR(255) NOT NULL, body VARCHAR(1024) NOT NULL, revealed_date VARCHAR(255) DEFAULT NULL, INDEX IDX_EFB778DD243DFBFE (casebook_subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE casebook_subject_note (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, casebook_subject_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', title VARCHAR(255) NOT NULL, body VARCHAR(1024) NOT NULL, INDEX IDX_6802F18B03A8386 (created_by_id), INDEX IDX_6802F18243DFBFE (casebook_subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE casebook_subject ADD CONSTRAINT FK_911EC37B467F13AB FOREIGN KEY (casebook_id) REFERENCES casebook (id)');
        $this->addSql('ALTER TABLE casebook_subject_clue ADD CONSTRAINT FK_EFB778DD243DFBFE FOREIGN KEY (casebook_subject_id) REFERENCES casebook_subject (id)');
        $this->addSql('ALTER TABLE casebook_subject_note ADD CONSTRAINT FK_6802F18B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE casebook_subject_note ADD CONSTRAINT FK_6802F18243DFBFE FOREIGN KEY (casebook_subject_id) REFERENCES casebook_subject (id)');
        $this->addSql('ALTER TABLE user RENAME INDEX uniq_identifier_email TO UNIQ_8D93D649E7927C74');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE casebook_subject DROP FOREIGN KEY FK_911EC37B467F13AB');
        $this->addSql('ALTER TABLE casebook_subject_clue DROP FOREIGN KEY FK_EFB778DD243DFBFE');
        $this->addSql('ALTER TABLE casebook_subject_note DROP FOREIGN KEY FK_6802F18B03A8386');
        $this->addSql('ALTER TABLE casebook_subject_note DROP FOREIGN KEY FK_6802F18243DFBFE');
        $this->addSql('DROP TABLE casebook');
        $this->addSql('DROP TABLE casebook_subject');
        $this->addSql('DROP TABLE casebook_subject_clue');
        $this->addSql('DROP TABLE casebook_subject_note');
        $this->addSql('ALTER TABLE user RENAME INDEX uniq_8d93d649e7927c74 TO UNIQ_IDENTIFIER_EMAIL');
    }
}
