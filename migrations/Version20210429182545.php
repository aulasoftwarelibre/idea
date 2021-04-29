<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210429182545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Refactor is_online bool field to format string';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE idea ADD format VARCHAR(255) DEFAULT \'FACE_TO_FACE\' NOT NULL');
        $this->addSql('UPDATE idea SET format = "ONLINE" WHERE is_online = 1');
        $this->addSql('ALTER TABLE idea DROP is_online');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE idea ADD is_online TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('UPDATE idea SET is_online = 1 WHERE format = "ONLINE"');
        $this->addSql('ALTER TABLE idea DROP format');
    }
}
