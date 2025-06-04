<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250603100227 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE catalog_products (is_active BOOLEAN NOT NULL, identifier UUID NOT NULL, name VARCHAR(100) NOT NULL, base_cost_components JSON NOT NULL, total_base_cost_amount INT NOT NULL, total_base_cost_currency VARCHAR(3) NOT NULL, PRIMARY KEY(identifier))
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE auth_users ALTER identifier TYPE UUID
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN auth_users.identifier IS ''
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users ALTER identifier TYPE UUID
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN users.identifier IS ''
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE catalog_products
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users ALTER identifier TYPE UUID
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN users.identifier IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE auth_users ALTER identifier TYPE UUID
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN auth_users.identifier IS '(DC2Type:uuid)'
        SQL);
    }
}
