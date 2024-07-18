<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240718093916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT fk_8d93d649f603ee73');
        $this->addSql('DROP INDEX uniq_8d93d649f603ee73');
        $this->addSql('ALTER TABLE "user" DROP vendor_id');
        $this->addSql('ALTER TABLE vendor ADD vendor_id INT NOT NULL');
        $this->addSql('ALTER TABLE vendor ADD CONSTRAINT FK_F52233F6F603EE73 FOREIGN KEY (vendor_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F52233F6F603EE73 ON vendor (vendor_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" ADD vendor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT fk_8d93d649f603ee73 FOREIGN KEY (vendor_id) REFERENCES vendor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649f603ee73 ON "user" (vendor_id)');
        $this->addSql('ALTER TABLE vendor DROP CONSTRAINT FK_F52233F6F603EE73');
        $this->addSql('DROP INDEX UNIQ_F52233F6F603EE73');
        $this->addSql('ALTER TABLE vendor DROP vendor_id');
    }
}
