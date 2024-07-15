<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240711080253 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_identifier_email');
        $this->addSql('ALTER TABLE "user" ADD phone VARCHAR(30) NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER email DROP NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER full_name DROP NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER address DROP NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_PHONE ON "user" (phone)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_PHONE');
        $this->addSql('ALTER TABLE "user" DROP phone');
        $this->addSql('ALTER TABLE "user" ALTER email SET NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER full_name SET NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER address SET NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_identifier_email ON "user" (email)');
    }
}
