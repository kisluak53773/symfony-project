<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240715095639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE producer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE product_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE producer (id INT NOT NULL, title VARCHAR(40) NOT NULL, country VARCHAR(40) NOT NULL, address VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE product (id INT NOT NULL, producer_id INT NOT NULL, title VARCHAR(40) NOT NULL, description VARCHAR(400) NOT NULL, compound VARCHAR(255) NOT NULL, storage_conditions VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, weight VARCHAR(40) NOT NULL, price INT NOT NULL, image VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D34A04AD89B658FE ON product (producer_id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD89B658FE FOREIGN KEY (producer_id) REFERENCES producer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE producer_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE product_id_seq CASCADE');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04AD89B658FE');
        $this->addSql('DROP TABLE producer');
        $this->addSql('DROP TABLE product');
    }
}
