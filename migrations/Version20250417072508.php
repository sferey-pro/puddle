<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250417072508 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "user_logins" (
          id SERIAL NOT NULL,
          user_id INT NOT NULL,
          expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          hash TEXT NOT NULL,
          is_verified BOOLEAN NOT NULL,
          ip_address VARCHAR(255) NOT NULL,
          uuid UUID NOT NULL,
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_6341CC99A76ED395 ON "user_logins" (user_id)');
        $this->addSql('COMMENT ON COLUMN "user_logins".expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "user_logins".uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE
          "user_logins"
        ADD
          CONSTRAINT FK_6341CC99A76ED395 FOREIGN KEY (user_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user_logins" DROP CONSTRAINT FK_6341CC99A76ED395');
        $this->addSql('DROP TABLE "user_logins"');
    }
}
