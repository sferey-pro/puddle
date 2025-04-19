<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250418131742 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE additional_costs ADD created_by_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE additional_costs ADD updated_by_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE additional_costs ADD CONSTRAINT FK_D3EF2C99B03A8386 FOREIGN KEY (created_by_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE additional_costs ADD CONSTRAINT FK_D3EF2C99896DBBDE FOREIGN KEY (updated_by_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D3EF2C99B03A8386 ON additional_costs (created_by_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D3EF2C99896DBBDE ON additional_costs (updated_by_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE categories ADD created_by_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE categories ADD updated_by_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE categories ADD CONSTRAINT FK_3AF34668B03A8386 FOREIGN KEY (created_by_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE categories ADD CONSTRAINT FK_3AF34668896DBBDE FOREIGN KEY (updated_by_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_3AF34668B03A8386 ON categories (created_by_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_3AF34668896DBBDE ON categories (updated_by_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE products ADD created_by_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE products ADD updated_by_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5AB03A8386 FOREIGN KEY (created_by_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A896DBBDE FOREIGN KEY (updated_by_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B3BA5A5AB03A8386 ON products (created_by_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B3BA5A5A896DBBDE ON products (updated_by_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE raw_materials ADD created_by_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE raw_materials ADD updated_by_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE raw_materials ADD CONSTRAINT FK_84333784B03A8386 FOREIGN KEY (created_by_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE raw_materials ADD CONSTRAINT FK_84333784896DBBDE FOREIGN KEY (updated_by_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_84333784B03A8386 ON raw_materials (created_by_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_84333784896DBBDE ON raw_materials (updated_by_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE suppliers ADD created_by_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE suppliers ADD updated_by_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE suppliers ADD CONSTRAINT FK_AC28B95CB03A8386 FOREIGN KEY (created_by_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE suppliers ADD CONSTRAINT FK_AC28B95C896DBBDE FOREIGN KEY (updated_by_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_AC28B95CB03A8386 ON suppliers (created_by_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_AC28B95C896DBBDE ON suppliers (updated_by_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "categories" DROP CONSTRAINT FK_3AF34668B03A8386
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "categories" DROP CONSTRAINT FK_3AF34668896DBBDE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_3AF34668B03A8386
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_3AF34668896DBBDE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "categories" DROP created_by_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "categories" DROP updated_by_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "additional_costs" DROP CONSTRAINT FK_D3EF2C99B03A8386
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "additional_costs" DROP CONSTRAINT FK_D3EF2C99896DBBDE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_D3EF2C99B03A8386
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_D3EF2C99896DBBDE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "additional_costs" DROP created_by_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "additional_costs" DROP updated_by_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "raw_materials" DROP CONSTRAINT FK_84333784B03A8386
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "raw_materials" DROP CONSTRAINT FK_84333784896DBBDE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_84333784B03A8386
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_84333784896DBBDE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "raw_materials" DROP created_by_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "raw_materials" DROP updated_by_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "suppliers" DROP CONSTRAINT FK_AC28B95CB03A8386
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "suppliers" DROP CONSTRAINT FK_AC28B95C896DBBDE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_AC28B95CB03A8386
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_AC28B95C896DBBDE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "suppliers" DROP created_by_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "suppliers" DROP updated_by_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "products" DROP CONSTRAINT FK_B3BA5A5AB03A8386
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "products" DROP CONSTRAINT FK_B3BA5A5A896DBBDE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_B3BA5A5AB03A8386
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_B3BA5A5A896DBBDE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "products" DROP created_by_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "products" DROP updated_by_id
        SQL);
    }
}
