<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250509210151 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE "additional_costs" (id SERIAL NOT NULL, created_by_id INT NOT NULL, updated_by_id INT NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, price VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D3EF2C99B03A8386 ON "additional_costs" (created_by_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D3EF2C99896DBBDE ON "additional_costs" (updated_by_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN "additional_costs".uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "addresses" (id SERIAL NOT NULL, city VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN "addresses".uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "categories" (id SERIAL NOT NULL, created_by_id INT NOT NULL, updated_by_id INT NOT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(7) NOT NULL, slug VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_3AF34668989D9B62 ON "categories" (slug)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_3AF34668B03A8386 ON "categories" (created_by_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_3AF34668896DBBDE ON "categories" (updated_by_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN "categories".uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "contacts" (id SERIAL NOT NULL, category_id INT DEFAULT NULL, address_id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_3340157312469DE2 ON "contacts" (category_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_33401573F5B7AF75 ON "contacts" (address_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN "contacts".uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "products" (id SERIAL NOT NULL, category_id INT NOT NULL, created_by_id INT NOT NULL, updated_by_id INT NOT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, description TEXT DEFAULT NULL, slug VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_B3BA5A5A989D9B62 ON "products" (slug)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B3BA5A5A12469DE2 ON "products" (category_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B3BA5A5AB03A8386 ON "products" (created_by_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B3BA5A5A896DBBDE ON "products" (updated_by_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN "products".uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "raw_material_items" (id SERIAL NOT NULL, raw_material_list_id INT NOT NULL, raw_material VARCHAR(255) NOT NULL, quantity DOUBLE PRECISION NOT NULL, unit VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C128FCCC72EB7B5C ON "raw_material_items" (raw_material_list_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN "raw_material_items".uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "raw_material_lists" (id SERIAL NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN "raw_material_lists".uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "raw_materials" (id SERIAL NOT NULL, supplier_id INT DEFAULT NULL, category_id INT NOT NULL, created_by_id INT NOT NULL, updated_by_id INT NOT NULL, name VARCHAR(255) NOT NULL, unit_price DOUBLE PRECISION NOT NULL, price_variability BOOLEAN NOT NULL, unit VARCHAR(255) NOT NULL, total_cost DOUBLE PRECISION NOT NULL, notes TEXT DEFAULT NULL, link VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_843337842ADD6D8C ON "raw_materials" (supplier_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8433378412469DE2 ON "raw_materials" (category_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_84333784B03A8386 ON "raw_materials" (created_by_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_84333784896DBBDE ON "raw_materials" (updated_by_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN "raw_materials".uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "suppliers" (id SERIAL NOT NULL, created_by_id INT NOT NULL, updated_by_id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_AC28B95CB03A8386 ON "suppliers" (created_by_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_AC28B95C896DBBDE ON "suppliers" (updated_by_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN "suppliers".uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "user_logins" (id SERIAL NOT NULL, user_id INT NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, hash TEXT NOT NULL, is_verified BOOLEAN NOT NULL, ip_address VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6341CC99A76ED395 ON "user_logins" (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN "user_logins".expires_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN "user_logins".uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "user_social_networks" (id SERIAL NOT NULL, user_id INT NOT NULL, social_network VARCHAR(255) NOT NULL, social_id VARCHAR(255) NOT NULL, is_active BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_45544C88A76ED395 ON "user_social_networks" (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN "user_social_networks".uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "users" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL, locale VARCHAR(12) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "users" (email)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN "users".uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "additional_costs" ADD CONSTRAINT FK_D3EF2C99B03A8386 FOREIGN KEY (created_by_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "additional_costs" ADD CONSTRAINT FK_D3EF2C99896DBBDE FOREIGN KEY (updated_by_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "categories" ADD CONSTRAINT FK_3AF34668B03A8386 FOREIGN KEY (created_by_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "categories" ADD CONSTRAINT FK_3AF34668896DBBDE FOREIGN KEY (updated_by_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "contacts" ADD CONSTRAINT FK_3340157312469DE2 FOREIGN KEY (category_id) REFERENCES "categories" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "contacts" ADD CONSTRAINT FK_33401573F5B7AF75 FOREIGN KEY (address_id) REFERENCES "addresses" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "products" ADD CONSTRAINT FK_B3BA5A5A12469DE2 FOREIGN KEY (category_id) REFERENCES "categories" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "products" ADD CONSTRAINT FK_B3BA5A5AB03A8386 FOREIGN KEY (created_by_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "products" ADD CONSTRAINT FK_B3BA5A5A896DBBDE FOREIGN KEY (updated_by_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "raw_material_items" ADD CONSTRAINT FK_C128FCCC72EB7B5C FOREIGN KEY (raw_material_list_id) REFERENCES "raw_material_lists" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "raw_materials" ADD CONSTRAINT FK_843337842ADD6D8C FOREIGN KEY (supplier_id) REFERENCES "suppliers" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "raw_materials" ADD CONSTRAINT FK_8433378412469DE2 FOREIGN KEY (category_id) REFERENCES "categories" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "raw_materials" ADD CONSTRAINT FK_84333784B03A8386 FOREIGN KEY (created_by_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "raw_materials" ADD CONSTRAINT FK_84333784896DBBDE FOREIGN KEY (updated_by_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "suppliers" ADD CONSTRAINT FK_AC28B95CB03A8386 FOREIGN KEY (created_by_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "suppliers" ADD CONSTRAINT FK_AC28B95C896DBBDE FOREIGN KEY (updated_by_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user_logins" ADD CONSTRAINT FK_6341CC99A76ED395 FOREIGN KEY (user_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user_social_networks" ADD CONSTRAINT FK_45544C88A76ED395 FOREIGN KEY (user_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "additional_costs" DROP CONSTRAINT FK_D3EF2C99B03A8386
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "additional_costs" DROP CONSTRAINT FK_D3EF2C99896DBBDE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "categories" DROP CONSTRAINT FK_3AF34668B03A8386
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "categories" DROP CONSTRAINT FK_3AF34668896DBBDE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "contacts" DROP CONSTRAINT FK_3340157312469DE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "contacts" DROP CONSTRAINT FK_33401573F5B7AF75
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "products" DROP CONSTRAINT FK_B3BA5A5A12469DE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "products" DROP CONSTRAINT FK_B3BA5A5AB03A8386
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "products" DROP CONSTRAINT FK_B3BA5A5A896DBBDE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "raw_material_items" DROP CONSTRAINT FK_C128FCCC72EB7B5C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "raw_materials" DROP CONSTRAINT FK_843337842ADD6D8C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "raw_materials" DROP CONSTRAINT FK_8433378412469DE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "raw_materials" DROP CONSTRAINT FK_84333784B03A8386
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "raw_materials" DROP CONSTRAINT FK_84333784896DBBDE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "suppliers" DROP CONSTRAINT FK_AC28B95CB03A8386
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "suppliers" DROP CONSTRAINT FK_AC28B95C896DBBDE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user_logins" DROP CONSTRAINT FK_6341CC99A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user_social_networks" DROP CONSTRAINT FK_45544C88A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "additional_costs"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "addresses"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "categories"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "contacts"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "products"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "raw_material_items"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "raw_material_lists"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "raw_materials"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "suppliers"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "user_logins"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "user_social_networks"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "users"
        SQL);
    }
}
