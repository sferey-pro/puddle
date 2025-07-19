<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250721141559_Identity extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE identity_attached_identifiers (identifier_type VARCHAR(50) NOT NULL, identifier_value VARCHAR(255) NOT NULL, is_primary BOOLEAN DEFAULT false NOT NULL, is_verified BOOLEAN DEFAULT false NOT NULL, attached_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, verified_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, id UUID NOT NULL, user_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_identifier_type_value ON identity_attached_identifiers (identifier_type, identifier_value)');
        $this->addSql('COMMENT ON TABLE identity_attached_identifiers IS \'Relational identifier storage - one row per identifier\'');
        $this->addSql('COMMENT ON COLUMN identity_attached_identifiers.identifier_type IS \'Type of identifier (email, phone, loyalty_card, etc.)\'');
        $this->addSql('COMMENT ON COLUMN identity_attached_identifiers.identifier_value IS \'Actual identifier value\'');
        $this->addSql('COMMENT ON COLUMN identity_attached_identifiers.is_primary IS \'Primary identifier for this user\'');
        $this->addSql('COMMENT ON COLUMN identity_attached_identifiers.is_verified IS \'Whether this identifier is verified\'');
        $this->addSql('COMMENT ON COLUMN identity_attached_identifiers.attached_at IS \'When this identifier was attached\'');
        $this->addSql('COMMENT ON COLUMN identity_attached_identifiers.verified_at IS \'When this identifier was verified\'');
        $this->addSql('CREATE TABLE identity_user_identities (created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, user_id UUID NOT NULL, PRIMARY KEY(user_id))');
        $this->addSql('COMMENT ON TABLE identity_user_identities IS \'User identity aggregate root - relational approach\'');
        $this->addSql('COMMENT ON COLUMN identity_user_identities.created_at IS \'When this user identity was created\'');
        $this->addSql('COMMENT ON COLUMN identity_user_identities.updated_at IS \'Last update timestamp\'');

        $this->addSql('ALTER TABLE identity_attached_identifiers ADD CONSTRAINT FK_BD5BBBE5A76ED395 FOREIGN KEY (user_id) REFERENCES identity_user_identities (user_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE identity_attached_identifiers DROP CONSTRAINT FK_BD5BBBE5A76ED395');
        $this->addSql('DROP TABLE identity_attached_identifiers');
        $this->addSql('DROP TABLE identity_user_identities');
    }
}
