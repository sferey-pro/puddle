<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250605201403 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE cost_management_cost_item (description TEXT DEFAULT NULL, status TEXT NOT NULL, identifier UUID NOT NULL, name VARCHAR(255) NOT NULL, target_amount INT NOT NULL, target_currency VARCHAR(3) NOT NULL, current_amount INT NOT NULL, current_currency VARCHAR(3) NOT NULL, coverage_start_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, coverage_end_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(identifier))
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE cost_management_cost_item
        SQL);
    }
}
