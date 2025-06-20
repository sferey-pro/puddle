<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250617133637_Profile extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE user_profiles (user_id UUID NOT NULL, first_name TEXT DEFAULT NULL, last_name TEXT DEFAULT NULL, date_of_birth DATE DEFAULT NULL, username VARCHAR(255) NOT NULL, display_name VARCHAR(255) NOT NULL, PRIMARY KEY(user_id))
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users ADD registered_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users RENAME COLUMN name TO status
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users ALTER status TYPE VARCHAR(255)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE user_profiles
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users DROP registered_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users RENAME COLUMN status TO name
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users ALTER name TYPE VARCHAR(255)
        SQL);
    }
}
