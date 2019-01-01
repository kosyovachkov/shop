<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190101103424 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ordered_products ADD userOrder_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ordered_products ADD CONSTRAINT FK_39EA292558ACF019 FOREIGN KEY (userOrder_id) REFERENCES user_orders (id)');
        $this->addSql('CREATE INDEX IDX_39EA292558ACF019 ON ordered_products (userOrder_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ordered_products DROP FOREIGN KEY FK_39EA292558ACF019');
        $this->addSql('DROP INDEX IDX_39EA292558ACF019 ON ordered_products');
        $this->addSql('ALTER TABLE ordered_products DROP userOrder_id');
    }
}
