<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250404090122 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE product_raw_material_id_seq CASCADE');
        $this->addSql('CREATE TABLE raw_material_item (id SERIAL NOT NULL, raw_material_list_id INT NOT NULL, raw_material VARCHAR(255) NOT NULL, quantity DOUBLE PRECISION NOT NULL, unit VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_468B176B72EB7B5C ON raw_material_item (raw_material_list_id)');
        $this->addSql('CREATE TABLE raw_material_list (id SERIAL NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE raw_material_item ADD CONSTRAINT FK_468B176B72EB7B5C FOREIGN KEY (raw_material_list_id) REFERENCES raw_material_list (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_raw_material DROP CONSTRAINT fk_fb8082d4584665a');
        $this->addSql('ALTER TABLE product_raw_material DROP CONSTRAINT fk_fb8082d693ca4a7');
        $this->addSql('DROP TABLE product_raw_material');
        $this->addSql('ALTER TABLE raw_material ALTER supplier_id DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE product_raw_material_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE product_raw_material (id SERIAL NOT NULL, product_id INT NOT NULL, raw_material_id INT NOT NULL, quantity DOUBLE PRECISION NOT NULL, unit VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_fb8082d693ca4a7 ON product_raw_material (raw_material_id)');
        $this->addSql('CREATE INDEX idx_fb8082d4584665a ON product_raw_material (product_id)');
        $this->addSql('ALTER TABLE product_raw_material ADD CONSTRAINT fk_fb8082d4584665a FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_raw_material ADD CONSTRAINT fk_fb8082d693ca4a7 FOREIGN KEY (raw_material_id) REFERENCES raw_material (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE raw_material_item DROP CONSTRAINT FK_468B176B72EB7B5C');
        $this->addSql('DROP TABLE raw_material_item');
        $this->addSql('DROP TABLE raw_material_list');
        $this->addSql('ALTER TABLE raw_material ALTER supplier_id SET NOT NULL');
    }
}
