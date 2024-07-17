<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240717124526 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE vendor_product_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE vendor_product (id INT NOT NULL, vendor_id INT NOT NULL, product_id INT NOT NULL, price NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CD3D3CC2F603EE73 ON vendor_product (vendor_id)');
        $this->addSql('CREATE INDEX IDX_CD3D3CC24584665A ON vendor_product (product_id)');
        $this->addSql('ALTER TABLE vendor_product ADD CONSTRAINT FK_CD3D3CC2F603EE73 FOREIGN KEY (vendor_id) REFERENCES vendor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vendor_product ADD CONSTRAINT FK_CD3D3CC24584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product DROP price');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE vendor_product_id_seq CASCADE');
        $this->addSql('ALTER TABLE vendor_product DROP CONSTRAINT FK_CD3D3CC2F603EE73');
        $this->addSql('ALTER TABLE vendor_product DROP CONSTRAINT FK_CD3D3CC24584665A');
        $this->addSql('DROP TABLE vendor_product');
        $this->addSql('ALTER TABLE product ADD price INT NOT NULL');
    }
}
