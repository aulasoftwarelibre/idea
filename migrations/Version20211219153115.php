<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211219153115 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('UPDATE idea SET closed = 0');
        $this->addSql('UPDATE idea SET closed = 1 WHERE state = "rejected"');
        $this->addSql('ALTER TABLE idea DROP state');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE idea ADD state VARCHAR(32) CHARACTER SET utf8mb4 DEFAULT \'approved\' NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('UPDATE idea SET state = "rejected" WHERE closed = 1');
        $this->addSql('UPDATE idea SET closed = 1');
    }
}
