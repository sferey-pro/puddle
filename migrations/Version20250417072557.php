<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250417072557 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "user_social_networks" (
          id SERIAL NOT NULL,
          user_id INT NOT NULL,
          social_network VARCHAR(255) NOT NULL,
          social_id VARCHAR(255) NOT NULL,
          is_active BOOLEAN NOT NULL,
          uuid UUID NOT NULL,
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_45544C88A76ED395 ON "user_social_networks" (user_id)');
        $this->addSql('COMMENT ON COLUMN "user_social_networks".uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE
          "user_social_networks"
        ADD
          CONSTRAINT FK_45544C88A76ED395 FOREIGN KEY (user_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user_social_networks" DROP CONSTRAINT FK_45544C88A76ED395');
        $this->addSql('DROP TABLE "user_social_networks"');
    }
}
