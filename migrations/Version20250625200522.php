<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250625200522 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_72A0ED268DFE9A8 ON lucca_department
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_department ADD showInHomePage TINYINT(1) DEFAULT 1 NOT NULL, DROP domainName
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_department ADD domainName VARCHAR(255) NOT NULL, DROP showInHomePage
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_72A0ED268DFE9A8 ON lucca_department (domainName)
        SQL);
    }
}
