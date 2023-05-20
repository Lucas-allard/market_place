<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230513070257 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE brand ADD picture_id INT NOT NULL, ADD slug VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE brand ADD CONSTRAINT FK_1C52F958EE45BDBF FOREIGN KEY (picture_id) REFERENCES picture (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1C52F958EE45BDBF ON brand (picture_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE brand DROP FOREIGN KEY FK_1C52F958EE45BDBF');
        $this->addSql('DROP INDEX UNIQ_1C52F958EE45BDBF ON brand');
        $this->addSql('ALTER TABLE brand DROP picture_id, DROP slug');
    }
}
