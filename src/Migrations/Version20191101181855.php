<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191101181855 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_957A64797039F86D ON fos_user');
        $this->addSql('DROP INDEX UNIQ_957A6479A0D96FBF ON fos_user');
        $this->addSql('ALTER TABLE fos_user CHANGE email_canonical email_canonical VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user ADD is_external TINYINT(1) NOT NULL, ADD has_profile TINYINT(1) NOT NULL, ADD alias VARCHAR(32) NOT NULL, DROP ssp_id');
        $this->addSql('ALTER TABLE idea ADD external_num_seats INT NOT NULL');
        $this->addSql('UPDATE fos_user SET is_external = 0, has_profile = 0, alias = username');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE idea DROP external_num_seats');
        $this->addSql('ALTER TABLE fos_user ADD ssp_id VARCHAR(50) DEFAULT NULL COLLATE utf8_unicode_ci, DROP is_external, DROP has_profile, DROP alias');
        $this->addSql('ALTER TABLE fos_user CHANGE email_canonical email_canonical VARCHAR(180) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_957A6479A0D96FBF ON fos_user (email_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_957A64797039F86D ON fos_user (ssp_id)');
    }
}
