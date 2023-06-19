<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230521205340 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_caracteristic (product_id INT NOT NULL, caracteristic_id INT NOT NULL, INDEX IDX_41789FB64584665A (product_id), INDEX IDX_41789FB681194CF4 (caracteristic_id), PRIMARY KEY(product_id, caracteristic_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_caracteristic ADD CONSTRAINT FK_41789FB64584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_caracteristic ADD CONSTRAINT FK_41789FB681194CF4 FOREIGN KEY (caracteristic_id) REFERENCES caracteristic (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE brand ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE caracteristic DROP FOREIGN KEY FK_9B9583444584665A');
        $this->addSql('DROP INDEX IDX_9B9583444584665A ON caracteristic');
        $this->addSql('ALTER TABLE caracteristic ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL, DROP product_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_caracteristic DROP FOREIGN KEY FK_41789FB64584665A');
        $this->addSql('ALTER TABLE product_caracteristic DROP FOREIGN KEY FK_41789FB681194CF4');
        $this->addSql('DROP TABLE product_caracteristic');
        $this->addSql('ALTER TABLE brand DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE caracteristic ADD product_id INT NOT NULL, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE caracteristic ADD CONSTRAINT FK_9B9583444584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_9B9583444584665A ON caracteristic (product_id)');
    }
}
