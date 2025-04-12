<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250326144501 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE supplier (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE raw_material ADD supplier_id INT NOT NULL');
        $this->addSql('ALTER TABLE raw_material DROP supplier');
        $this->addSql('ALTER TABLE raw_material ADD CONSTRAINT FK_821C2A462ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_821C2A462ADD6D8C ON raw_material (supplier_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE raw_material DROP CONSTRAINT FK_821C2A462ADD6D8C');
        $this->addSql('DROP TABLE supplier');
        $this->addSql('DROP INDEX IDX_821C2A462ADD6D8C');
        $this->addSql('ALTER TABLE raw_material ADD supplier VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE raw_material DROP supplier_id');
    }
}
