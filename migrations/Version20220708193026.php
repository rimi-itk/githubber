<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220708193026 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE repository ADD organization_id INT NOT NULL');
        $this->addSql('ALTER TABLE repository ADD CONSTRAINT FK_5CFE57CD32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('CREATE INDEX IDX_5CFE57CD32C8A3DE ON repository (organization_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE repository DROP FOREIGN KEY FK_5CFE57CD32C8A3DE');
        $this->addSql('DROP INDEX IDX_5CFE57CD32C8A3DE ON repository');
        $this->addSql('ALTER TABLE repository DROP organization_id');
    }
}
