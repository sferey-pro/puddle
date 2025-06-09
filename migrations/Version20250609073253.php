<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250609073253 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE cost_contribution (contributed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, id UUID NOT NULL, amount INT NOT NULL, currency VARCHAR(3) NOT NULL, source_product_id UUID NOT NULL, cost_item_id UUID NOT NULL, PRIMARY KEY(id, source_product_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5C3A13F5401DA61 ON cost_contribution (cost_item_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE cost_item (description TEXT DEFAULT NULL, status VARCHAR(255) NOT NULL, id UUID NOT NULL, name VARCHAR(255) NOT NULL, target_amount INT NOT NULL, target_currency VARCHAR(3) NOT NULL, coverage_start_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, coverage_end_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cost_contribution ADD CONSTRAINT FK_5C3A13F5401DA61 FOREIGN KEY (cost_item_id) REFERENCES cost_item (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE cost_contribution DROP CONSTRAINT FK_5C3A13F5401DA61
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE cost_contribution
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE cost_item
        SQL);
    }
}
