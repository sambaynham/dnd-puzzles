<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250920182854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE puzzle_instance (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, instance_code VARCHAR(255) NOT NULL, config JSON NOT NULL COMMENT \'(DC2Type:json)\', publication_date DATETIME DEFAULT NULL, INDEX IDX_166C5C8FE48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE puzzle_instance ADD CONSTRAINT FK_166C5C8FE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE puzzle_template DROP FOREIGN KEY FK_C33CF2D2F675F31B');
        $this->addSql('ALTER TABLE puzzle_template_puzzle_category DROP FOREIGN KEY FK_FD9C85E2E1A9DB15');
        $this->addSql('ALTER TABLE puzzle_template_puzzle_category DROP FOREIGN KEY FK_FD9C85E2F635494F');
        $this->addSql('DROP TABLE puzzle_template');
        $this->addSql('DROP TABLE puzzle_template_puzzle_category');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE puzzle_template (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', configuration JSON NOT NULL COMMENT \'(DC2Type:json)\', description VARCHAR(1024) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_C33CF2D2F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE puzzle_template_puzzle_category (puzzle_template_id INT NOT NULL, puzzle_category_id INT NOT NULL, INDEX IDX_FD9C85E2E1A9DB15 (puzzle_template_id), INDEX IDX_FD9C85E2F635494F (puzzle_category_id), PRIMARY KEY(puzzle_template_id, puzzle_category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE puzzle_template ADD CONSTRAINT FK_C33CF2D2F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE puzzle_template_puzzle_category ADD CONSTRAINT FK_FD9C85E2E1A9DB15 FOREIGN KEY (puzzle_template_id) REFERENCES puzzle_template (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE puzzle_template_puzzle_category ADD CONSTRAINT FK_FD9C85E2F635494F FOREIGN KEY (puzzle_category_id) REFERENCES puzzle_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE puzzle_instance DROP FOREIGN KEY FK_166C5C8FE48FD905');
        $this->addSql('DROP TABLE puzzle_instance');
    }
}
