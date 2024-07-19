<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240717114333 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE vendor_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE vendor (id INT NOT NULL, title VARCHAR(40) NOT NULL, address VARCHAR(255) NOT NULL, inn VARCHAR(10) NOT NULL, registration_authority VARCHAR(100) NOT NULL, registration_date DATE NOT NULL, registration_certificate_date DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE "user" ADD vendor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649F603EE73 FOREIGN KEY (vendor_id) REFERENCES vendor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F603EE73 ON "user" (vendor_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649F603EE73');
        $this->addSql('DROP SEQUENCE vendor_id_seq CASCADE');
        $this->addSql('DROP TABLE vendor');
        $this->addSql('DROP INDEX UNIQ_8D93D649F603EE73');
        $this->addSql('ALTER TABLE "user" DROP vendor_id');
    }
}
