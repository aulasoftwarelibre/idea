<?php

declare(strict_types=1);

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180131181854 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE idea ADD starts_at DATETIME DEFAULT NULL, ADD location VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user ADD telegram_chat_id VARCHAR(64) DEFAULT NULL, ADD telegram_secret_token VARCHAR(100) DEFAULT NULL, ADD telegram_secret_token_expires_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user ADD CONSTRAINT FK_957A647941DC10D3 FOREIGN KEY (telegram_chat_id) REFERENCES telegram_chat (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_957A647941DC10D3 ON fos_user (telegram_chat_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user DROP FOREIGN KEY FK_957A647941DC10D3');
        $this->addSql('DROP INDEX UNIQ_957A647941DC10D3 ON fos_user');
        $this->addSql('ALTER TABLE fos_user DROP telegram_chat_id, DROP telegram_secret_token, DROP telegram_secret_token_expires_at');
        $this->addSql('ALTER TABLE idea DROP starts_at, DROP location');
    }
}
