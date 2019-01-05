<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190105093159 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE carts DROP FOREIGN KEY FK_4E004AACA76ED395');
        $this->addSql('ALTER TABLE carts ADD CONSTRAINT FK_4E004AACA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E91AD5CDBF');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E91AD5CDBF FOREIGN KEY (cart_id) REFERENCES carts (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE carts DROP FOREIGN KEY FK_4E004AACA76ED395');
        $this->addSql('ALTER TABLE carts ADD CONSTRAINT FK_4E004AACA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E91AD5CDBF');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E91AD5CDBF FOREIGN KEY (cart_id) REFERENCES carts (id)');
    }
}
