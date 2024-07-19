<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240718094245 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vendor DROP CONSTRAINT fk_f52233f6f603ee73');
        $this->addSql('DROP INDEX uniq_f52233f6f603ee73');
        $this->addSql('ALTER TABLE vendor RENAME COLUMN vendor_id TO user_id');
        $this->addSql('ALTER TABLE vendor ADD CONSTRAINT FK_F52233F6A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F52233F6A76ED395 ON vendor (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE vendor DROP CONSTRAINT FK_F52233F6A76ED395');
        $this->addSql('DROP INDEX UNIQ_F52233F6A76ED395');
        $this->addSql('ALTER TABLE vendor RENAME COLUMN user_id TO vendor_id');
        $this->addSql('ALTER TABLE vendor ADD CONSTRAINT fk_f52233f6f603ee73 FOREIGN KEY (vendor_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_f52233f6f603ee73 ON vendor (vendor_id)');
    }
}
