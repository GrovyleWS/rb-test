<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240229152205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C3EBEDDC8');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C4EB16A88');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C3EBEDDC8 FOREIGN KEY (left_team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C4EB16A88 FOREIGN KEY (right_team_id) REFERENCES team (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C3EBEDDC8');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C4EB16A88');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C3EBEDDC8 FOREIGN KEY (left_team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C4EB16A88 FOREIGN KEY (right_team_id) REFERENCES team (id)');
    }
}
