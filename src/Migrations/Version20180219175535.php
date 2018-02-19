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
class Version20180219175535 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comment CHANGE thread_id thread_id VARCHAR(255) DEFAULT NULL, CHANGE author_id author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE thread CHANGE last_comment_at last_comment_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE idea CHANGE closed closed TINYINT(1) DEFAULT NULL, CHANGE starts_at starts_at DATETIME DEFAULT NULL, CHANGE location location VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE telegram_chat CHANGE title title VARCHAR(256) DEFAULT NULL, CHANGE username username VARCHAR(256) DEFAULT NULL, CHANGE notifications notifications JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user CHANGE degree_id degree_id INT DEFAULT NULL, CHANGE telegram_chat_id telegram_chat_id VARCHAR(64) DEFAULT NULL, CHANGE salt salt VARCHAR(255) DEFAULT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT NULL, CHANGE password_requested_at password_requested_at DATETIME DEFAULT NULL, CHANGE date_of_birth date_of_birth DATETIME DEFAULT NULL, CHANGE firstname firstname VARCHAR(64) DEFAULT NULL, CHANGE lastname lastname VARCHAR(64) DEFAULT NULL, CHANGE website website VARCHAR(64) DEFAULT NULL, CHANGE biography biography VARCHAR(1000) DEFAULT NULL, CHANGE gender gender VARCHAR(1) DEFAULT NULL, CHANGE locale locale VARCHAR(8) DEFAULT NULL, CHANGE timezone timezone VARCHAR(64) DEFAULT NULL, CHANGE phone phone VARCHAR(64) DEFAULT NULL, CHANGE facebook_uid facebook_uid VARCHAR(255) DEFAULT NULL, CHANGE facebook_name facebook_name VARCHAR(255) DEFAULT NULL, CHANGE facebook_data facebook_data JSON DEFAULT NULL, CHANGE twitter_uid twitter_uid VARCHAR(255) DEFAULT NULL, CHANGE twitter_name twitter_name VARCHAR(255) DEFAULT NULL, CHANGE twitter_data twitter_data JSON DEFAULT NULL, CHANGE gplus_uid gplus_uid VARCHAR(255) DEFAULT NULL, CHANGE gplus_name gplus_name VARCHAR(255) DEFAULT NULL, CHANGE gplus_data gplus_data JSON DEFAULT NULL, CHANGE token token VARCHAR(255) DEFAULT NULL, CHANGE two_step_code two_step_code VARCHAR(255) DEFAULT NULL, CHANGE collective collective VARCHAR(32) DEFAULT NULL, CHANGE year year VARCHAR(4) DEFAULT NULL, CHANGE image_name image_name VARCHAR(255) DEFAULT NULL, CHANGE image_original_name image_original_name VARCHAR(255) DEFAULT NULL, CHANGE image_mime_type image_mime_type VARCHAR(255) DEFAULT NULL, CHANGE image_size image_size INT DEFAULT NULL, CHANGE nic nic VARCHAR(32) DEFAULT NULL, CHANGE image_dimensions image_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', CHANGE telegram_secret_token telegram_secret_token VARCHAR(100) DEFAULT NULL, CHANGE telegram_secret_token_expires_at telegram_secret_token_expires_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comment CHANGE thread_id thread_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE author_id author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user CHANGE telegram_chat_id telegram_chat_id VARCHAR(64) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE degree_id degree_id INT DEFAULT NULL, CHANGE salt salt VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE last_login last_login DATETIME DEFAULT \'NULL\', CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE password_requested_at password_requested_at DATETIME DEFAULT \'NULL\', CHANGE date_of_birth date_of_birth DATETIME DEFAULT \'NULL\', CHANGE firstname firstname VARCHAR(64) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE lastname lastname VARCHAR(64) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE website website VARCHAR(64) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE biography biography VARCHAR(1000) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE gender gender VARCHAR(1) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE locale locale VARCHAR(8) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE timezone timezone VARCHAR(64) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE phone phone VARCHAR(64) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE facebook_uid facebook_uid VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE facebook_name facebook_name VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE facebook_data facebook_data JSON DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE twitter_uid twitter_uid VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE twitter_name twitter_name VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE twitter_data twitter_data JSON DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE gplus_uid gplus_uid VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE gplus_name gplus_name VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE gplus_data gplus_data JSON DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE token token VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE two_step_code two_step_code VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE telegram_secret_token telegram_secret_token VARCHAR(100) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE telegram_secret_token_expires_at telegram_secret_token_expires_at DATETIME DEFAULT \'NULL\', CHANGE collective collective VARCHAR(32) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE year year VARCHAR(4) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE nic nic VARCHAR(32) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE image_name image_name VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE image_original_name image_original_name VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE image_mime_type image_mime_type VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE image_size image_size INT DEFAULT NULL, CHANGE image_dimensions image_dimensions LONGTEXT DEFAULT \'NULL\' COLLATE utf8_unicode_ci COMMENT \'(DC2Type:simple_array)\'');
        $this->addSql('ALTER TABLE idea CHANGE closed closed TINYINT(1) DEFAULT \'NULL\', CHANGE starts_at starts_at DATETIME DEFAULT \'NULL\', CHANGE location location VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE telegram_chat CHANGE title title VARCHAR(256) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE username username VARCHAR(256) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE notifications notifications LONGTEXT DEFAULT NULL COLLATE utf8mb4_bin');
        $this->addSql('ALTER TABLE thread CHANGE last_comment_at last_comment_at DATETIME DEFAULT \'NULL\'');
    }
}
