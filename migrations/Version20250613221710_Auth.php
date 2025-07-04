<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250613221710_Auth extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

        public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE user_accounts (id UUID NOT NULL, roles JSON NOT NULL, is_verified BOOLEAN NOT NULL, is_active BOOLEAN NOT NULL, first_login_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, username VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_AUTH_USERS_EMAIL ON user_accounts (email)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "user_logins" (id UUID NOT NULL, user_id UUID NOT NULL, is_verified BOOLEAN DEFAULT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, login_link_hash VARCHAR(255) NOT NULL, ip_address VARCHAR(32) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6341CC99A76ED395 ON "user_logins" (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "user_social_networks" (id UUID NOT NULL, social_id VARCHAR(255) NOT NULL, user_id UUID NOT NULL, is_active BOOLEAN DEFAULT NULL, social_network VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_45544C88A76ED395 ON "user_social_networks" (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user_logins" ADD CONSTRAINT FK_6341CC99A76ED395 FOREIGN KEY (user_id) REFERENCES user_accounts (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user_social_networks" ADD CONSTRAINT FK_45544C88A76ED395 FOREIGN KEY (user_id) REFERENCES user_accounts (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE "user_logins" DROP CONSTRAINT FK_6341CC99A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user_social_networks" DROP CONSTRAINT FK_45544C88A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_accounts
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "user_logins"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "user_social_networks"
        SQL);
    }
}
