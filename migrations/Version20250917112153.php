<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250917112153 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE puzzle_template_puzzle_category (puzzle_template_id INT NOT NULL, puzzle_category_id INT NOT NULL, INDEX IDX_FD9C85E2E1A9DB15 (puzzle_template_id), INDEX IDX_FD9C85E2F635494F (puzzle_category_id), PRIMARY KEY(puzzle_template_id, puzzle_category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE puzzle_template_puzzle_category ADD CONSTRAINT FK_FD9C85E2E1A9DB15 FOREIGN KEY (puzzle_template_id) REFERENCES puzzle_template (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE puzzle_template_puzzle_category ADD CONSTRAINT FK_FD9C85E2F635494F FOREIGN KEY (puzzle_category_id) REFERENCES puzzle_category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE puzzle_template_puzzle_category DROP FOREIGN KEY FK_FD9C85E2E1A9DB15');
        $this->addSql('ALTER TABLE puzzle_template_puzzle_category DROP FOREIGN KEY FK_FD9C85E2F635494F');
        $this->addSql('DROP TABLE puzzle_template_puzzle_category');
    }
}
