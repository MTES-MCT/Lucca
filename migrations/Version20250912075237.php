<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250912075237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_952C7069DC43AF6E ON lucca_minute_folder');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_952C7069DC43AF6EAE80F5DF ON lucca_minute_folder (num, department_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_952C7069DC43AF6EAE80F5DF ON lucca_minute_folder');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_952C7069DC43AF6E ON lucca_minute_folder (num)');
    }
}
