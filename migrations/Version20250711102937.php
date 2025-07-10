<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250711102937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "auth_password_credentials" (user_id UUID NOT NULL, password VARCHAR DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "auth_user_activities" (user_id UUID NOT NULL, login_count INT NOT NULL, first_login_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, last_login_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE auth_user_accounts DROP first_login_at');
        $this->addSql('ALTER TABLE auth_user_accounts RENAME COLUMN password TO phone');
        $this->addSql('ALTER TABLE auth_user_accounts ALTER phone TYPE VARCHAR');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_AUTH_USERS_PHONE ON auth_user_accounts (phone)');
        $this->addSql('ALTER TABLE users ADD phone VARCHAR DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE users RENAME COLUMN registered_at TO created_at');
        $this->addSql('ALTER TABLE users ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_USERS_PHONE ON users (phone)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE "auth_password_credentials"');
        $this->addSql('DROP TABLE "auth_user_activities"');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_USERS_PHONE');
        $this->addSql('ALTER TABLE "users" ADD registered_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE "users" DROP phone');
        $this->addSql('ALTER TABLE "users" DROP created_at');
        $this->addSql('ALTER TABLE "users" DROP updated_at');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_AUTH_USERS_PHONE');
        $this->addSql('ALTER TABLE "auth_user_accounts" ADD first_login_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE "auth_user_accounts" RENAME COLUMN phone TO password');
        $this->addSql('ALTER TABLE "auth_user_accounts" ALTER password TYPE VARCHAR');
    }
}
