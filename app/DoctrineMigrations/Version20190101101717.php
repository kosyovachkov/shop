<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190101101717 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_orders DROP FOREIGN KEY FK_807DE6D3C1A7BCDD');
        $this->addSql('DROP INDEX IDX_807DE6D3C1A7BCDD ON user_orders');
        $this->addSql('ALTER TABLE user_orders CHANGE use_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_orders ADD CONSTRAINT FK_807DE6D3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_807DE6D3A76ED395 ON user_orders (user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_orders DROP FOREIGN KEY FK_807DE6D3A76ED395');
        $this->addSql('DROP INDEX IDX_807DE6D3A76ED395 ON user_orders');
        $this->addSql('ALTER TABLE user_orders CHANGE user_id use_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_orders ADD CONSTRAINT FK_807DE6D3C1A7BCDD FOREIGN KEY (use_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_807DE6D3C1A7BCDD ON user_orders (use_id)');
    }
}
