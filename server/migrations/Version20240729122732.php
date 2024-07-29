<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240729122732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_product DROP CONSTRAINT fk_2890ccaaf603ee73');
        $this->addSql('ALTER TABLE cart_product DROP CONSTRAINT fk_2890ccaa4584665a');
        $this->addSql('DROP INDEX idx_2890ccaa4584665a');
        $this->addSql('DROP INDEX idx_2890ccaaf603ee73');
        $this->addSql('ALTER TABLE cart_product ADD vendor_product_id INT NOT NULL');
        $this->addSql('ALTER TABLE cart_product DROP vendor_id');
        $this->addSql('ALTER TABLE cart_product DROP product_id');
        $this->addSql('ALTER TABLE cart_product ADD CONSTRAINT FK_2890CCAADFF1B23D FOREIGN KEY (vendor_product_id) REFERENCES vendor_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_2890CCAADFF1B23D ON cart_product (vendor_product_id)');
        $this->addSql('ALTER TABLE order_product DROP CONSTRAINT fk_2530ade64584665a');
        $this->addSql('ALTER TABLE order_product DROP CONSTRAINT fk_2530ade6f603ee73');
        $this->addSql('DROP INDEX idx_2530ade6f603ee73');
        $this->addSql('DROP INDEX idx_2530ade64584665a');
        $this->addSql('ALTER TABLE order_product ADD vendor_product_id INT NOT NULL');
        $this->addSql('ALTER TABLE order_product DROP product_id');
        $this->addSql('ALTER TABLE order_product DROP vendor_id');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE6DFF1B23D FOREIGN KEY (vendor_product_id) REFERENCES vendor_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_2530ADE6DFF1B23D ON order_product (vendor_product_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE order_product DROP CONSTRAINT FK_2530ADE6DFF1B23D');
        $this->addSql('DROP INDEX IDX_2530ADE6DFF1B23D');
        $this->addSql('ALTER TABLE order_product ADD vendor_id INT NOT NULL');
        $this->addSql('ALTER TABLE order_product RENAME COLUMN vendor_product_id TO product_id');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT fk_2530ade64584665a FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT fk_2530ade6f603ee73 FOREIGN KEY (vendor_id) REFERENCES vendor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_2530ade6f603ee73 ON order_product (vendor_id)');
        $this->addSql('CREATE INDEX idx_2530ade64584665a ON order_product (product_id)');
        $this->addSql('ALTER TABLE cart_product DROP CONSTRAINT FK_2890CCAADFF1B23D');
        $this->addSql('DROP INDEX IDX_2890CCAADFF1B23D');
        $this->addSql('ALTER TABLE cart_product ADD product_id INT NOT NULL');
        $this->addSql('ALTER TABLE cart_product RENAME COLUMN vendor_product_id TO vendor_id');
        $this->addSql('ALTER TABLE cart_product ADD CONSTRAINT fk_2890ccaaf603ee73 FOREIGN KEY (vendor_id) REFERENCES vendor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cart_product ADD CONSTRAINT fk_2890ccaa4584665a FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_2890ccaa4584665a ON cart_product (product_id)');
        $this->addSql('CREATE INDEX idx_2890ccaaf603ee73 ON cart_product (vendor_id)');
    }
}
