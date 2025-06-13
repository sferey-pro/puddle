<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250613153856 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE sales_order (status VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, id UUID NOT NULL, user_identifier UUID NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE sales_order_line (quantity INT NOT NULL, id UUID NOT NULL, product_identifier UUID NOT NULL, unit_price_amount INT NOT NULL, unit_price_currency VARCHAR(3) NOT NULL, order_id UUID DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_93D9398D8D9F6D38 ON sales_order_line (order_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sales_order_line ADD CONSTRAINT FK_93D9398D8D9F6D38 FOREIGN KEY (order_id) REFERENCES sales_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cost_contribution DROP CONSTRAINT cost_contribution_pkey
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cost_contribution RENAME COLUMN source_product_id TO source_product_identifier
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cost_contribution ALTER source_product_identifier TYPE UUID
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cost_contribution ADD PRIMARY KEY (id, source_product_identifier)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE sales_order_line DROP CONSTRAINT FK_93D9398D8D9F6D38
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE sales_order
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE sales_order_line
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX cost_contribution_pkey
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cost_contribution RENAME COLUMN source_product_identifier TO source_product_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cost_contribution ALTER source_product_id TYPE UUID
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cost_contribution ADD PRIMARY KEY (id, source_product_id)
        SQL);
    }
}
