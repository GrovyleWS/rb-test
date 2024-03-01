<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240229151429 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, tournament_id INT NOT NULL, left_team_id INT NOT NULL, right_team_id INT NOT NULL, date DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', INDEX IDX_232B318C33D1A3E7 (tournament_id), INDEX IDX_232B318C3EBEDDC8 (left_team_id), INDEX IDX_232B318C4EB16A88 (right_team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tournament (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tournament_team (tournament_id INT NOT NULL, team_id INT NOT NULL, INDEX IDX_F36D142133D1A3E7 (tournament_id), INDEX IDX_F36D1421296CD8AE (team_id), PRIMARY KEY(tournament_id, team_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C33D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C3EBEDDC8 FOREIGN KEY (left_team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C4EB16A88 FOREIGN KEY (right_team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE tournament_team ADD CONSTRAINT FK_F36D142133D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tournament_team ADD CONSTRAINT FK_F36D1421296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C33D1A3E7');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C3EBEDDC8');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C4EB16A88');
        $this->addSql('ALTER TABLE tournament_team DROP FOREIGN KEY FK_F36D142133D1A3E7');
        $this->addSql('ALTER TABLE tournament_team DROP FOREIGN KEY FK_F36D1421296CD8AE');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE tournament');
        $this->addSql('DROP TABLE tournament_team');
    }
}
