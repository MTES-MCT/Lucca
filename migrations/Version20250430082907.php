<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250430082907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent_linked_department DROP FOREIGN KEY FK_AE2F95D525F06C53
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent_linked_department DROP FOREIGN KEY FK_AE2F95D5AE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_adherent_linked_department
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent DROP INDEX UNIQ_208D2875A76ED395, ADD INDEX IDX_208D2875A76ED395 (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent ADD department_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent ADD CONSTRAINT FK_208D2875AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_208D2875AE80F5DF ON lucca_adherent (department_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_adherent_linked_department (adherent_id INT NOT NULL, department_id INT NOT NULL, INDEX IDX_AE2F95D5AE80F5DF (department_id), INDEX IDX_AE2F95D525F06C53 (adherent_id), PRIMARY KEY(adherent_id, department_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent_linked_department ADD CONSTRAINT FK_AE2F95D525F06C53 FOREIGN KEY (adherent_id) REFERENCES lucca_adherent (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent_linked_department ADD CONSTRAINT FK_AE2F95D5AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent DROP INDEX IDX_208D2875A76ED395, ADD UNIQUE INDEX UNIQ_208D2875A76ED395 (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent DROP FOREIGN KEY FK_208D2875AE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_208D2875AE80F5DF ON lucca_adherent
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent DROP department_id
        SQL);
    }
}
