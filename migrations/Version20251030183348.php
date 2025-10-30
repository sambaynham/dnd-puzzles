<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251030183348 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE abuse_report (id INT AUTO_INCREMENT NOT NULL, reported_user_id INT NOT NULL, reporting_user_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', reason VARCHAR(1024) NOT NULL, checked_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', confirmed_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_21AF999BE7566E (reported_user_id), INDEX IDX_21AF999B713FF03D (reporting_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE abuse_report ADD CONSTRAINT FK_21AF999BE7566E FOREIGN KEY (reported_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE abuse_report ADD CONSTRAINT FK_21AF999B713FF03D FOREIGN KEY (reporting_user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abuse_report DROP FOREIGN KEY FK_21AF999BE7566E');
        $this->addSql('ALTER TABLE abuse_report DROP FOREIGN KEY FK_21AF999B713FF03D');
        $this->addSql('DROP TABLE abuse_report');
    }
}
