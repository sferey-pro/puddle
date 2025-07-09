<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250708213807_CostManagement extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE "cost_management_cost_contribution" (source_product_id UUID NOT NULL, status VARCHAR(255) NOT NULL, contributed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, id UUID NOT NULL, amount INT NOT NULL, currency VARCHAR(3) NOT NULL, cost_item_id UUID NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_21FCB5AA5401DA61 ON "cost_management_cost_contribution" (cost_item_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "cost_management_cost_item" (name VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, is_template BOOLEAN DEFAULT false NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, id UUID NOT NULL, target_amount INT NOT NULL, target_currency VARCHAR(3) NOT NULL, coverage_start_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, coverage_end_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "cost_management_recurring_cost" (template_cost_item_id UUID NOT NULL, status VARCHAR(255) NOT NULL, last_generated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, id UUID NOT NULL, recurrence_frequency VARCHAR(255) NOT NULL, recurrence_day INT DEFAULT NULL, recurrence_rule VARCHAR(100) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "cost_management_cost_contribution" ADD CONSTRAINT FK_21FCB5AA5401DA61 FOREIGN KEY (cost_item_id) REFERENCES "cost_management_cost_item" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE "cost_management_cost_contribution" DROP CONSTRAINT FK_21FCB5AA5401DA61
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "cost_management_cost_contribution"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "cost_management_cost_item"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "cost_management_recurring_cost"
        SQL);
    }
}
