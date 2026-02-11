<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260211150355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lucca_setting ADD extraParam JSON DEFAULT NULL, ADD media_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_setting ADD CONSTRAINT FK_11348FE2EA9FDD75EA9FDD75 FOREIGN KEY (media_id) REFERENCES lucca_media (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_11348FE2EA9FDD75 ON lucca_setting (media_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lucca_setting DROP FOREIGN KEY FK_11348FE2EA9FDD75EA9FDD75');
        $this->addSql('DROP INDEX IDX_11348FE2EA9FDD75 ON lucca_setting');
        $this->addSql('ALTER TABLE lucca_setting DROP extraParam, DROP media_id');
    }
}
