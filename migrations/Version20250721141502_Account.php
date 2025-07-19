<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250721141502_Account extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "accounts" (verified_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_by UUID DEFAULT NULL, updated_by UUID DEFAULT NULL, version INT DEFAULT 0 NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, id UUID NOT NULL, state_name VARCHAR(20) NOT NULL, state_reason VARCHAR(255) DEFAULT NULL, state_metadata JSON DEFAULT NULL, state_changed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, state_expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, state_is_active BOOLEAN DEFAULT false NOT NULL, state_priority INT DEFAULT 0 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON TABLE "accounts" IS \'Core Account aggregate - manages account lifecycle and identity\'');
        $this->addSql('COMMENT ON COLUMN "accounts".verified_at IS \'When the account was verified\'');
        $this->addSql('COMMENT ON COLUMN "accounts".created_at IS \'Creation timestamp\'');
        $this->addSql('COMMENT ON COLUMN "accounts".updated_at IS \'Last update timestamp\'');
        $this->addSql('COMMENT ON COLUMN "accounts".created_by IS \'User who created this account\'');
        $this->addSql('COMMENT ON COLUMN "accounts".updated_by IS \'User who last updated this account\'');
        $this->addSql('COMMENT ON COLUMN "accounts".version IS \'Version for optimistic locking\'');
        $this->addSql('COMMENT ON COLUMN "accounts".deleted_at IS \'Soft deletion timestamp\'');
        $this->addSql('COMMENT ON COLUMN "accounts".state_name IS \'State name for reconstruction and queries\'');
        $this->addSql('COMMENT ON COLUMN "accounts".state_reason IS \'Reason for state change (suspension/lock)\'');
        $this->addSql('COMMENT ON COLUMN "accounts".state_metadata IS \'State-specific context data (no index for PostgreSQL compatibility)\'');
        $this->addSql('COMMENT ON COLUMN "accounts".state_changed_at IS \'When this state was entered\'');
        $this->addSql('COMMENT ON COLUMN "accounts".state_expires_at IS \'Expiration for time-limited states\'');
        $this->addSql('COMMENT ON COLUMN "accounts".state_is_active IS \'Quick active state flag for queries\'');
        $this->addSql('COMMENT ON COLUMN "accounts".state_priority IS \'State priority for workflow resolution\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE "accounts"');
    }
}
