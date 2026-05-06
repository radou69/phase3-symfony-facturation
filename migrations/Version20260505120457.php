<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260505120457 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__client AS SELECT id, name, email, phone, address, siret, rib FROM client');
        $this->addSql('DROP TABLE client');
        $this->addSql('CREATE TABLE client (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, siret VARCHAR(255) DEFAULT NULL, rib VARCHAR(255) NOT NULL, owner_id INTEGER NOT NULL, CONSTRAINT FK_C74404557E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO client (id, name, email, phone, address, siret, rib) SELECT id, name, email, phone, address, siret, rib FROM __temp__client');
        $this->addSql('DROP TABLE __temp__client');
        $this->addSql('CREATE INDEX IDX_C74404557E3C61F9 ON client (owner_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__invoice AS SELECT id, number, status, total_ttc, created_at, owner_id, client_id FROM invoice');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('CREATE TABLE invoice (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, number VARCHAR(255) NOT NULL, status VARCHAR(50) NOT NULL, total_ttc DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, owner_id INTEGER NOT NULL, client_id INTEGER NOT NULL, CONSTRAINT FK_906517447E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9065174419EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO invoice (id, number, status, total_ttc, created_at, owner_id, client_id) SELECT id, number, status, total_ttc, created_at, owner_id, client_id FROM __temp__invoice');
        $this->addSql('DROP TABLE __temp__invoice');
        $this->addSql('CREATE INDEX IDX_9065174419EB6921 ON invoice (client_id)');
        $this->addSql('CREATE INDEX IDX_906517447E3C61F9 ON invoice (owner_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9065174496901F54 ON invoice (number)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__invoice_item AS SELECT id, quantity, product_id FROM invoice_item');
        $this->addSql('DROP TABLE invoice_item');
        $this->addSql('CREATE TABLE invoice_item (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, quantity INTEGER NOT NULL, product_id INTEGER NOT NULL, invoice_id INTEGER NOT NULL, CONSTRAINT FK_1DDE477B4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_1DDE477B2989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO invoice_item (id, quantity, product_id) SELECT id, quantity, product_id FROM __temp__invoice_item');
        $this->addSql('DROP TABLE __temp__invoice_item');
        $this->addSql('CREATE INDEX IDX_1DDE477B4584665A ON invoice_item (product_id)');
        $this->addSql('CREATE INDEX IDX_1DDE477B2989F1FD ON invoice_item (invoice_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__client AS SELECT id, name, email, phone, address, siret, rib FROM client');
        $this->addSql('DROP TABLE client');
        $this->addSql('CREATE TABLE client (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, siret VARCHAR(255) DEFAULT NULL, rib VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO client (id, name, email, phone, address, siret, rib) SELECT id, name, email, phone, address, siret, rib FROM __temp__client');
        $this->addSql('DROP TABLE __temp__client');
        $this->addSql('CREATE TEMPORARY TABLE __temp__invoice AS SELECT id, number, status, total_ttc, created_at, owner_id, client_id FROM invoice');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('CREATE TABLE invoice (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, number VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, total_ttc DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, owner_id INTEGER NOT NULL, client_id INTEGER NOT NULL, CONSTRAINT FK_906517447E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9065174419EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO invoice (id, number, status, total_ttc, created_at, owner_id, client_id) SELECT id, number, status, total_ttc, created_at, owner_id, client_id FROM __temp__invoice');
        $this->addSql('DROP TABLE __temp__invoice');
        $this->addSql('CREATE INDEX IDX_906517447E3C61F9 ON invoice (owner_id)');
        $this->addSql('CREATE INDEX IDX_9065174419EB6921 ON invoice (client_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__invoice_item AS SELECT id, quantity, product_id FROM invoice_item');
        $this->addSql('DROP TABLE invoice_item');
        $this->addSql('CREATE TABLE invoice_item (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, quantity INTEGER NOT NULL, product_id INTEGER NOT NULL, CONSTRAINT FK_1DDE477B4584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO invoice_item (id, quantity, product_id) SELECT id, quantity, product_id FROM __temp__invoice_item');
        $this->addSql('DROP TABLE __temp__invoice_item');
        $this->addSql('CREATE INDEX IDX_1DDE477B4584665A ON invoice_item (product_id)');
    }
}
