<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250721141629_Saga extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "saga_processes" (current_state VARCHAR(100) NOT NULL, context JSON NOT NULL, history JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, id UUID NOT NULL, saga_type VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON TABLE "saga_processes" IS \'Single table for all saga processes with inheritance\'');
        $this->addSql('COMMENT ON COLUMN "saga_processes".current_state IS \'Current state in the saga workflow\'');
        $this->addSql('COMMENT ON COLUMN "saga_processes".context IS \'Saga-specific business context data\'');
        $this->addSql('COMMENT ON COLUMN "saga_processes".history IS \'History of completed transitions\'');
        $this->addSql('COMMENT ON COLUMN "saga_processes".created_at IS \'When this saga process was started\'');
        $this->addSql('COMMENT ON COLUMN "saga_processes".updated_at IS \'Last update timestamp\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE "saga_processes"');
    }
}
