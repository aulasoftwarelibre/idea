<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191202174646 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user ADD version INT DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE log_policy DROP FOREIGN KEY FK_E58756B4A76ED395');
        $this->addSql('ALTER TABLE log_policy ADD CONSTRAINT FK_E58756B4A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user DROP version');
        $this->addSql('ALTER TABLE log_policy DROP FOREIGN KEY FK_E58756B4A76ED395');
        $this->addSql('ALTER TABLE log_policy ADD CONSTRAINT FK_E58756B4A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
    }
}
