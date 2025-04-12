<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250326144108 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_raw_material (id SERIAL NOT NULL, product_id INT NOT NULL, raw_material_id INT NOT NULL, quantity DOUBLE PRECISION NOT NULL, unit VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FB8082D4584665A ON product_raw_material (product_id)');
        $this->addSql('CREATE INDEX IDX_FB8082D693CA4A7 ON product_raw_material (raw_material_id)');
        $this->addSql('CREATE TABLE raw_material (id SERIAL NOT NULL, category_id INT NOT NULL, name VARCHAR(255) NOT NULL, unit_price DOUBLE PRECISION NOT NULL, supplier VARCHAR(255) DEFAULT NULL, price_variability BOOLEAN NOT NULL, unit VARCHAR(255) NOT NULL, total_cost DOUBLE PRECISION NOT NULL, notes TEXT DEFAULT NULL, link VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_821C2A4612469DE2 ON raw_material (category_id)');
        $this->addSql('ALTER TABLE product_raw_material ADD CONSTRAINT FK_FB8082D4584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_raw_material ADD CONSTRAINT FK_FB8082D693CA4A7 FOREIGN KEY (raw_material_id) REFERENCES raw_material (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE raw_material ADD CONSTRAINT FK_821C2A4612469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE product_raw_material DROP CONSTRAINT FK_FB8082D4584665A');
        $this->addSql('ALTER TABLE product_raw_material DROP CONSTRAINT FK_FB8082D693CA4A7');
        $this->addSql('ALTER TABLE raw_material DROP CONSTRAINT FK_821C2A4612469DE2');
        $this->addSql('DROP TABLE product_raw_material');
        $this->addSql('DROP TABLE raw_material');
    }
}
