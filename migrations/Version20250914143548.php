<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250914143548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, games_master_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_232B318C3C75A0F1 (games_master_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_user (game_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_6686BA65E48FD905 (game_id), INDEX IDX_6686BA65A76ED395 (user_id), PRIMARY KEY(game_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C3C75A0F1 FOREIGN KEY (games_master_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game_user ADD CONSTRAINT FK_6686BA65E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_user ADD CONSTRAINT FK_6686BA65A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD username VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C3C75A0F1');
        $this->addSql('ALTER TABLE game_user DROP FOREIGN KEY FK_6686BA65E48FD905');
        $this->addSql('ALTER TABLE game_user DROP FOREIGN KEY FK_6686BA65A76ED395');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_user');
        $this->addSql('ALTER TABLE user DROP username');
    }
}
