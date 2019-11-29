<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191129105233 extends AbstractMigration
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
        $this->addSql('CREATE TABLE log_policy (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, create_at DATETIME NOT NULL, mandatory TINYINT(1) NOT NULL, version VARCHAR(255) NOT NULL, tag VARCHAR(255) NOT NULL, INDEX IDX_E58756B4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE log_policy ADD CONSTRAINT FK_E58756B4A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('DROP TABLE telegram_chat');
        $this->addSql('DROP INDEX UNIQ_957A64797039F86D ON fos_user');
        $this->addSql('DROP INDEX UNIQ_957A6479A0D96FBF ON fos_user');
        $this->addSql('DROP INDEX UNIQ_957A647941DC10D3 ON fos_user');
        $this->addSql('ALTER TABLE fos_user ADD is_external TINYINT(1) NOT NULL DEFAULT 0, ADD alias VARCHAR(32) NOT NULL, DROP telegram_chat_id, DROP telegram_secret_token, DROP telegram_secret_token_expires_at, DROP ssp_id, CHANGE email_canonical email_canonical VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE idea ADD external_num_seats INT NOT NULL');
        $this->addSql('ALTER TABLE fos_group ADD icon VARCHAR(32) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE telegram_chat (id VARCHAR(64) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, type VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, title VARCHAR(256) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, username VARCHAR(256) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, active TINYINT(1) NOT NULL, notifications LONGTEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:json)\', welcome_message LONGTEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, first_name VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, last_name VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE log_policy');
        $this->addSql('ALTER TABLE fos_group DROP icon');
        $this->addSql('ALTER TABLE fos_user ADD telegram_chat_id VARCHAR(64) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, ADD telegram_secret_token VARCHAR(100) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, ADD telegram_secret_token_expires_at DATETIME DEFAULT NULL, ADD ssp_id VARCHAR(50) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, DROP is_external, DROP alias, CHANGE email_canonical email_canonical VARCHAR(180) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE fos_user ADD CONSTRAINT FK_957A647941DC10D3 FOREIGN KEY (telegram_chat_id) REFERENCES telegram_chat (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_957A64797039F86D ON fos_user (ssp_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_957A6479A0D96FBF ON fos_user (email_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_957A647941DC10D3 ON fos_user (telegram_chat_id)');
        $this->addSql('ALTER TABLE idea DROP external_num_seats');
    }
}
