<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250417072642 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "categories" (
          id SERIAL NOT NULL,
          name VARCHAR(255) NOT NULL,
          color VARCHAR(7) NOT NULL,
          uuid UUID NOT NULL,
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN "categories".uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE "products" (
          id SERIAL NOT NULL,
          category_id INT NOT NULL,
          name VARCHAR(255) NOT NULL,
          price DOUBLE PRECISION NOT NULL,
          description TEXT DEFAULT NULL,
          slug VARCHAR(255) NOT NULL,
          uuid UUID NOT NULL,
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B3BA5A5A989D9B62 ON "products" (slug)');
        $this->addSql('CREATE INDEX IDX_B3BA5A5A12469DE2 ON "products" (category_id)');
        $this->addSql('COMMENT ON COLUMN "products".uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE
          "products"
        ADD
          CONSTRAINT FK_B3BA5A5A12469DE2 FOREIGN KEY (category_id) REFERENCES "categories" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "products" DROP CONSTRAINT FK_B3BA5A5A12469DE2');
        $this->addSql('DROP TABLE "categories"');
        $this->addSql('DROP TABLE "products"');
    }
}
