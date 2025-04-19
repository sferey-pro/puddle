<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250418141519 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE "addresses" (id SERIAL NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, city VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN "addresses".uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "contacts" (id SERIAL NOT NULL, category_id INT DEFAULT NULL, address_id INT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))
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
            ALTER TABLE "contacts" ADD CONSTRAINT FK_3340157312469DE2 FOREIGN KEY (category_id) REFERENCES "categories" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "contacts" ADD CONSTRAINT FK_33401573F5B7AF75 FOREIGN KEY (address_id) REFERENCES "addresses" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "contacts" DROP CONSTRAINT FK_3340157312469DE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "contacts" DROP CONSTRAINT FK_33401573F5B7AF75
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "addresses"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "contacts"
        SQL);
    }
}
