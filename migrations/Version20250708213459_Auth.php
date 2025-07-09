<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250708213459_Auth extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE "auth_password_reset_requests" (user_id UUID NOT NULL, requested_email VARCHAR DEFAULT NULL, ip_address VARCHAR DEFAULT NULL, selector VARCHAR(32) NOT NULL, hashed_token VARCHAR DEFAULT NULL, is_used BOOLEAN DEFAULT false NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, id UUID NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_214019119692E25D ON "auth_password_reset_requests" (selector)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "auth_social_link" (is_active BOOLEAN DEFAULT false NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, id UUID NOT NULL, social_id VARCHAR(255) NOT NULL, social_network VARCHAR(255) NOT NULL, user_id UUID NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B5AF380DA76ED395 ON "auth_social_link" (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "auth_user_accounts" (password VARCHAR DEFAULT NULL, email VARCHAR DEFAULT NULL, roles JSON NOT NULL, is_verified BOOLEAN DEFAULT false NOT NULL, is_active BOOLEAN DEFAULT true NOT NULL, first_login_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, id UUID NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_AUTH_USERS_EMAIL ON "auth_user_accounts" (email)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "auth_user_logins" (ip_address VARCHAR DEFAULT NULL, is_verified BOOLEAN DEFAULT false NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, id UUID NOT NULL, hash VARCHAR NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, user_id UUID NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_AF53BE01A76ED395 ON "auth_user_logins" (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "auth_social_link" ADD CONSTRAINT FK_B5AF380DA76ED395 FOREIGN KEY (user_id) REFERENCES "auth_user_accounts" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "auth_user_logins" ADD CONSTRAINT FK_AF53BE01A76ED395 FOREIGN KEY (user_id) REFERENCES "auth_user_accounts" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE "auth_social_link" DROP CONSTRAINT FK_B5AF380DA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "auth_user_logins" DROP CONSTRAINT FK_AF53BE01A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "auth_password_reset_requests"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "auth_social_link"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "auth_user_accounts"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "auth_user_logins"
        SQL);
    }
}
