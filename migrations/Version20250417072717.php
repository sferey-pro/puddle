<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250417072717 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "raw_material_items" (
          id SERIAL NOT NULL,
          raw_material_list_id INT NOT NULL,
          raw_material VARCHAR(255) NOT NULL,
          quantity DOUBLE PRECISION NOT NULL,
          unit VARCHAR(255) NOT NULL,
          uuid UUID NOT NULL,
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_C128FCCC72EB7B5C ON "raw_material_items" (raw_material_list_id)');
        $this->addSql('COMMENT ON COLUMN "raw_material_items".uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE "raw_material_lists" (
          id SERIAL NOT NULL,
          uuid UUID NOT NULL,
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN "raw_material_lists".uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE "raw_materials" (
          id SERIAL NOT NULL,
          supplier_id INT DEFAULT NULL,
          category_id INT NOT NULL,
          name VARCHAR(255) NOT NULL,
          unit_price DOUBLE PRECISION NOT NULL,
          price_variability BOOLEAN NOT NULL,
          unit VARCHAR(255) NOT NULL,
          total_cost DOUBLE PRECISION NOT NULL,
          notes TEXT DEFAULT NULL,
          link VARCHAR(255) NOT NULL,
          uuid UUID NOT NULL,
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_843337842ADD6D8C ON "raw_materials" (supplier_id)');
        $this->addSql('CREATE INDEX IDX_8433378412469DE2 ON "raw_materials" (category_id)');
        $this->addSql('COMMENT ON COLUMN "raw_materials".uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE
          "raw_material_items"
        ADD
          CONSTRAINT FK_C128FCCC72EB7B5C FOREIGN KEY (raw_material_list_id) REFERENCES "raw_material_lists" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          "raw_materials"
        ADD
          CONSTRAINT FK_843337842ADD6D8C FOREIGN KEY (supplier_id) REFERENCES "suppliers" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          "raw_materials"
        ADD
          CONSTRAINT FK_8433378412469DE2 FOREIGN KEY (category_id) REFERENCES "categories" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "raw_material_items" DROP CONSTRAINT FK_C128FCCC72EB7B5C');
        $this->addSql('ALTER TABLE "raw_materials" DROP CONSTRAINT FK_843337842ADD6D8C');
        $this->addSql('ALTER TABLE "raw_materials" DROP CONSTRAINT FK_8433378412469DE2');
        $this->addSql('DROP TABLE "raw_material_items"');
        $this->addSql('DROP TABLE "raw_material_lists"');
        $this->addSql('DROP TABLE "raw_materials"');
    }
}
