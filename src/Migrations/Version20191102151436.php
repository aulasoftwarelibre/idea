<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191102151436 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user DROP FOREIGN KEY FK_957A647941DC10D3');
        $this->addSql('DROP TABLE telegram_chat');
        $this->addSql('DROP INDEX UNIQ_957A647941DC10D3 ON fos_user');
        $this->addSql('ALTER TABLE fos_user DROP telegram_chat_id, DROP telegram_secret_token, DROP telegram_secret_token_expires_at');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE telegram_chat (id VARCHAR(64) NOT NULL COLLATE utf8_unicode_ci, type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, title VARCHAR(256) DEFAULT NULL COLLATE utf8_unicode_ci, username VARCHAR(256) DEFAULT NULL COLLATE utf8_unicode_ci, active TINYINT(1) NOT NULL, notifications LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json)\', welcome_message LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, first_name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, last_name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE fos_user ADD telegram_chat_id VARCHAR(64) DEFAULT NULL COLLATE utf8_unicode_ci, ADD telegram_secret_token VARCHAR(100) DEFAULT NULL COLLATE utf8_unicode_ci, ADD telegram_secret_token_expires_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user ADD CONSTRAINT FK_957A647941DC10D3 FOREIGN KEY (telegram_chat_id) REFERENCES telegram_chat (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_957A647941DC10D3 ON fos_user (telegram_chat_id)');
    }
}
