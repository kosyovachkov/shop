<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190110082333 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ordered_products DROP FOREIGN KEY FK_39EA29251AD5CDBF');
        $this->addSql('ALTER TABLE ordered_products ADD CONSTRAINT FK_39EA29251AD5CDBF FOREIGN KEY (cart_id) REFERENCES carts (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ordered_products DROP FOREIGN KEY FK_39EA29251AD5CDBF');
        $this->addSql('ALTER TABLE ordered_products ADD CONSTRAINT FK_39EA29251AD5CDBF FOREIGN KEY (cart_id) REFERENCES carts (id)');
    }
}
