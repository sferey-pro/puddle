<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250727125213_Authentication extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE access_credentials (user_id UUID DEFAULT NULL, identifier VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, used_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, metadata JSON DEFAULT \'{}\', id UUID NOT NULL, type VARCHAR(20) NOT NULL, token VARCHAR(255) DEFAULT NULL, code CHAR(6) DEFAULT NULL, attempts INT DEFAULT 0, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6090A5205F37A13B ON access_credentials (token)');
        $this->addSql('COMMENT ON COLUMN access_credentials.user_id IS \'Associated user ID (null for new registrations)\'');
        $this->addSql('COMMENT ON COLUMN access_credentials.identifier IS \'Email or phone number\'');
        $this->addSql('COMMENT ON COLUMN access_credentials.expires_at IS \'Expiration time for the credential\'');
        $this->addSql('COMMENT ON COLUMN access_credentials.used_at IS \'When the credential was used (null if not used)\'');
        $this->addSql('COMMENT ON COLUMN access_credentials.metadata IS \'Additional data: IP, user agent, etc.\'');
        $this->addSql('COMMENT ON COLUMN access_credentials.token IS \'Unique token for magic link\'');
        $this->addSql('COMMENT ON COLUMN access_credentials.code IS \'6-digit OTP code\'');
        $this->addSql('COMMENT ON COLUMN access_credentials.attempts IS \'Number of verification attempts\'');
        $this->addSql('CREATE TABLE auth_login_attempts (user_id UUID DEFAULT NULL, identifier VARCHAR(255) NOT NULL, attempted_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, successful BOOLEAN NOT NULL, method VARCHAR(50) NOT NULL, ip_address VARCHAR(45) NOT NULL, user_agent TEXT DEFAULT NULL, failure_reason VARCHAR(255) DEFAULT NULL, id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE blocked_ips (reason TEXT NOT NULL, blocked_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_by VARCHAR(255) DEFAULT \'system\' NOT NULL, ip_address VARCHAR(45) NOT NULL, PRIMARY KEY(ip_address))');
        $this->addSql('COMMENT ON COLUMN blocked_ips.reason IS \'Reason for blocking this IP\'');
        $this->addSql('COMMENT ON COLUMN blocked_ips.expires_at IS \'When the block expires (null = permanent)\'');
        $this->addSql('COMMENT ON COLUMN blocked_ips.created_by IS \'Who created this block (system or admin email)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE access_credentials');
        $this->addSql('DROP TABLE auth_login_attempts');
        $this->addSql('DROP TABLE blocked_ips');
    }
}
