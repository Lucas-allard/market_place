<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230705124528 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_item_seller (id INT AUTO_INCREMENT NOT NULL, seller_id INT NOT NULL, order_id INT NOT NULL, status VARCHAR(255) NOT NULL, shipping_date DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_6651D9B58DE820D9 (seller_id), INDEX IDX_6651D9B58D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_item_seller ADD CONSTRAINT FK_6651D9B58DE820D9 FOREIGN KEY (seller_id) REFERENCES seller (id)');
        $this->addSql('ALTER TABLE order_item_seller ADD CONSTRAINT FK_6651D9B58D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE `order` DROP shipping_date');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_item_seller DROP FOREIGN KEY FK_6651D9B58DE820D9');
        $this->addSql('ALTER TABLE order_item_seller DROP FOREIGN KEY FK_6651D9B58D9F6D38');
        $this->addSql('DROP TABLE order_item_seller');
        $this->addSql('ALTER TABLE `order` ADD shipping_date DATETIME DEFAULT NULL');
    }
}
