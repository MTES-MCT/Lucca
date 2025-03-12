<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250312121902 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Add department relation to tables
        $this->addSql('ALTER TABLE lucca_adherent ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_adherent ADD CONSTRAINT FK_208D2875AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_208D2875AE80F5DF ON lucca_adherent (department_id)');
        $this->addSql('ALTER TABLE lucca_adherent_agent ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_adherent_agent ADD CONSTRAINT FK_2193A0B0AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_2193A0B0AE80F5DF ON lucca_adherent_agent (department_id)');
        $this->addSql('ALTER TABLE lucca_checklist ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_checklist ADD CONSTRAINT FK_3104D71CAE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_3104D71CAE80F5DF ON lucca_checklist (department_id)');
        $this->addSql('ALTER TABLE lucca_content_area ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_content_area ADD CONSTRAINT FK_11F2D0DEAE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_11F2D0DEAE80F5DF ON lucca_content_area (department_id)');
        $this->addSql('ALTER TABLE lucca_content_page ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_content_page ADD CONSTRAINT FK_D26C5B96AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_D26C5B96AE80F5DF ON lucca_content_page (department_id)');
        $this->addSql('ALTER TABLE lucca_content_subarea ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_content_subarea ADD CONSTRAINT FK_80F3844BAE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_80F3844BAE80F5DF ON lucca_content_subarea (department_id)');
        $this->addSql('ALTER TABLE lucca_minute ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_minute ADD CONSTRAINT FK_901A2828AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_901A2828AE80F5DF ON lucca_minute (department_id)');
        $this->addSql('ALTER TABLE lucca_minute_closure ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_minute_closure ADD CONSTRAINT FK_99C56459AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_99C56459AE80F5DF ON lucca_minute_closure (department_id)');
        $this->addSql('ALTER TABLE lucca_minute_control ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_minute_control ADD CONSTRAINT FK_3C19C5C3AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_3C19C5C3AE80F5DF ON lucca_minute_control (department_id)');
        $this->addSql('ALTER TABLE lucca_minute_courier ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_minute_courier ADD CONSTRAINT FK_1ED1A5F4AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_1ED1A5F4AE80F5DF ON lucca_minute_courier (department_id)');
        $this->addSql('ALTER TABLE lucca_minute_decision ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_minute_decision ADD CONSTRAINT FK_671E77B3AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_671E77B3AE80F5DF ON lucca_minute_decision (department_id)');
        $this->addSql('ALTER TABLE lucca_minute_folder ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_minute_folder ADD CONSTRAINT FK_952C7069AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_952C7069AE80F5DF ON lucca_minute_folder (department_id)');
        $this->addSql('ALTER TABLE lucca_minute_human ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_minute_human ADD CONSTRAINT FK_F7A3D5FCAE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_F7A3D5FCAE80F5DF ON lucca_minute_human (department_id)');
        $this->addSql('ALTER TABLE lucca_minute_mayor_letter ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_minute_mayor_letter ADD CONSTRAINT FK_F13501DDAE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_F13501DDAE80F5DF ON lucca_minute_mayor_letter (department_id)');
        $this->addSql('ALTER TABLE lucca_minute_minute_story ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_minute_minute_story ADD CONSTRAINT FK_927A47D3AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_927A47D3AE80F5DF ON lucca_minute_minute_story (department_id)');
        $this->addSql('ALTER TABLE lucca_minute_plot ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_minute_plot ADD CONSTRAINT FK_1759B03AAE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_1759B03AAE80F5DF ON lucca_minute_plot (department_id)');
        $this->addSql('ALTER TABLE lucca_minute_profession ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_minute_profession ADD CONSTRAINT FK_AF63ACB9AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_AF63ACB9AE80F5DF ON lucca_minute_profession (department_id)');
        $this->addSql('ALTER TABLE lucca_minute_updating ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_minute_updating ADD CONSTRAINT FK_328C3F4AAE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_328C3F4AAE80F5DF ON lucca_minute_updating (department_id)');
        $this->addSql('ALTER TABLE lucca_model ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_model ADD CONSTRAINT FK_DB0D9941AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_DB0D9941AE80F5DF ON lucca_model (department_id)');
        $this->addSql('ALTER TABLE lucca_model_bloc ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_model_bloc ADD CONSTRAINT FK_5592B511AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_5592B511AE80F5DF ON lucca_model_bloc (department_id)');
        $this->addSql('ALTER TABLE lucca_model_margin ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_model_margin ADD CONSTRAINT FK_1B82EE35AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_1B82EE35AE80F5DF ON lucca_model_margin (department_id)');
        $this->addSql('ALTER TABLE lucca_model_page ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_model_page ADD CONSTRAINT FK_86E0966BAE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_86E0966BAE80F5DF ON lucca_model_page (department_id)');
        $this->addSql('ALTER TABLE lucca_natinf ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_natinf ADD CONSTRAINT FK_254257C9AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_254257C9AE80F5DF ON lucca_natinf (department_id)');
        $this->addSql('ALTER TABLE lucca_parameter_intercommunal ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_parameter_intercommunal ADD CONSTRAINT FK_2B223A6BAE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_2B223A6BAE80F5DF ON lucca_parameter_intercommunal (department_id)');
        $this->addSql('ALTER TABLE lucca_parameter_partner ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_parameter_partner ADD CONSTRAINT FK_A1FA699EAE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_A1FA699EAE80F5DF ON lucca_parameter_partner (department_id)');
        $this->addSql('ALTER TABLE lucca_parameter_service ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_parameter_service ADD CONSTRAINT FK_714CCD5AAE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_714CCD5AAE80F5DF ON lucca_parameter_service (department_id)');
        $this->addSql('ALTER TABLE lucca_parameter_town ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_parameter_town ADD CONSTRAINT FK_E7FAE590AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_E7FAE590AE80F5DF ON lucca_parameter_town (department_id)');
        $this->addSql('ALTER TABLE lucca_parameter_tribunal ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_parameter_tribunal ADD CONSTRAINT FK_3F7FE0EAAE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_3F7FE0EAAE80F5DF ON lucca_parameter_tribunal (department_id)');
        $this->addSql('ALTER TABLE lucca_proposal ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_proposal ADD CONSTRAINT FK_FBB4C67AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_FBB4C67AE80F5DF ON lucca_proposal (department_id)');
        $this->addSql('ALTER TABLE lucca_setting ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_setting ADD CONSTRAINT FK_11348FE2AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_11348FE2AE80F5DF ON lucca_setting (department_id)');
        $this->addSql('ALTER TABLE lucca_tag ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_tag ADD CONSTRAINT FK_4724FDFEAE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)');
        $this->addSql('CREATE INDEX IDX_4724FDFEAE80F5DF ON lucca_tag (department_id)');

        // Manual column updates
        $this->addSql('ALTER TABLE lucca_minute_folder CHANGE nature nature VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_model CHANGE privacy privacy JSON NOT NULL');

        // Remove all non-numeric characters from the latitude column, then change the column type to NUMERIC(40, 30)
        $this->addSql('UPDATE lucca_minute_plot SET latitude = REGEXP_REPLACE(latitude, \'[a-z]\', \'\')');
        $this->addSql('UPDATE lucca_minute_plot SET longitude = REGEXP_REPLACE(longitude, \'[a-z]\', \'\')');
        $this->addSql('ALTER TABLE lucca_minute_plot CHANGE latitude latitude NUMERIC(40, 30) DEFAULT NULL, CHANGE longitude longitude NUMERIC(40, 30) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lucca_content_page DROP FOREIGN KEY FK_D26C5B96AE80F5DF');
        $this->addSql('DROP INDEX IDX_D26C5B96AE80F5DF ON lucca_content_page');
        $this->addSql('ALTER TABLE lucca_content_page DROP department_id');
        $this->addSql('ALTER TABLE lucca_minute_decision DROP FOREIGN KEY FK_671E77B3AE80F5DF');
        $this->addSql('DROP INDEX IDX_671E77B3AE80F5DF ON lucca_minute_decision');
        $this->addSql('ALTER TABLE lucca_minute_decision DROP department_id');
        $this->addSql('ALTER TABLE lucca_model_bloc DROP FOREIGN KEY FK_5592B511AE80F5DF');
        $this->addSql('DROP INDEX IDX_5592B511AE80F5DF ON lucca_model_bloc');
        $this->addSql('ALTER TABLE lucca_model_bloc DROP department_id');
        $this->addSql('ALTER TABLE lucca_minute_courier DROP FOREIGN KEY FK_1ED1A5F4AE80F5DF');
        $this->addSql('DROP INDEX IDX_1ED1A5F4AE80F5DF ON lucca_minute_courier');
        $this->addSql('ALTER TABLE lucca_minute_courier DROP department_id');
        $this->addSql('ALTER TABLE lucca_adherent_agent DROP FOREIGN KEY FK_2193A0B0AE80F5DF');
        $this->addSql('DROP INDEX IDX_2193A0B0AE80F5DF ON lucca_adherent_agent');
        $this->addSql('ALTER TABLE lucca_adherent_agent DROP department_id');
        $this->addSql('ALTER TABLE lucca_checklist DROP FOREIGN KEY FK_3104D71CAE80F5DF');
        $this->addSql('DROP INDEX IDX_3104D71CAE80F5DF ON lucca_checklist');
        $this->addSql('ALTER TABLE lucca_checklist DROP department_id');
        $this->addSql('ALTER TABLE lucca_setting DROP FOREIGN KEY FK_11348FE2AE80F5DF');
        $this->addSql('DROP INDEX IDX_11348FE2AE80F5DF ON lucca_setting');
        $this->addSql('ALTER TABLE lucca_setting DROP department_id');
        $this->addSql('ALTER TABLE lucca_parameter_partner DROP FOREIGN KEY FK_A1FA699EAE80F5DF');
        $this->addSql('DROP INDEX IDX_A1FA699EAE80F5DF ON lucca_parameter_partner');
        $this->addSql('ALTER TABLE lucca_parameter_partner DROP department_id');
        $this->addSql('ALTER TABLE lucca_minute_human DROP FOREIGN KEY FK_F7A3D5FCAE80F5DF');
        $this->addSql('DROP INDEX IDX_F7A3D5FCAE80F5DF ON lucca_minute_human');
        $this->addSql('ALTER TABLE lucca_minute_human DROP department_id');
        $this->addSql('ALTER TABLE lucca_minute_mayor_letter DROP FOREIGN KEY FK_F13501DDAE80F5DF');
        $this->addSql('DROP INDEX IDX_F13501DDAE80F5DF ON lucca_minute_mayor_letter');
        $this->addSql('ALTER TABLE lucca_minute_mayor_letter DROP department_id');
        $this->addSql('ALTER TABLE lucca_minute_folder DROP FOREIGN KEY FK_952C7069AE80F5DF');
        $this->addSql('DROP INDEX IDX_952C7069AE80F5DF ON lucca_minute_folder');
        $this->addSql('ALTER TABLE lucca_minute_folder DROP department_id');
        $this->addSql('ALTER TABLE lucca_parameter_intercommunal DROP FOREIGN KEY FK_2B223A6BAE80F5DF');
        $this->addSql('DROP INDEX IDX_2B223A6BAE80F5DF ON lucca_parameter_intercommunal');
        $this->addSql('ALTER TABLE lucca_parameter_intercommunal DROP department_id');
        $this->addSql('ALTER TABLE lucca_minute_minute_story DROP FOREIGN KEY FK_927A47D3AE80F5DF');
        $this->addSql('DROP INDEX IDX_927A47D3AE80F5DF ON lucca_minute_minute_story');
        $this->addSql('ALTER TABLE lucca_minute_minute_story DROP department_id');
        $this->addSql('ALTER TABLE lucca_model_page DROP FOREIGN KEY FK_86E0966BAE80F5DF');
        $this->addSql('DROP INDEX IDX_86E0966BAE80F5DF ON lucca_model_page');
        $this->addSql('ALTER TABLE lucca_model_page DROP department_id');
        $this->addSql('ALTER TABLE lucca_minute_plot DROP FOREIGN KEY FK_1759B03AAE80F5DF');
        $this->addSql('DROP INDEX IDX_1759B03AAE80F5DF ON lucca_minute_plot');
        $this->addSql('ALTER TABLE lucca_minute_plot DROP department_id');
        $this->addSql('ALTER TABLE lucca_minute_control DROP FOREIGN KEY FK_3C19C5C3AE80F5DF');
        $this->addSql('DROP INDEX IDX_3C19C5C3AE80F5DF ON lucca_minute_control');
        $this->addSql('ALTER TABLE lucca_minute_control DROP department_id');
        $this->addSql('ALTER TABLE lucca_content_area DROP FOREIGN KEY FK_11F2D0DEAE80F5DF');
        $this->addSql('DROP INDEX IDX_11F2D0DEAE80F5DF ON lucca_content_area');
        $this->addSql('ALTER TABLE lucca_content_area DROP department_id');
        $this->addSql('ALTER TABLE lucca_minute_updating DROP FOREIGN KEY FK_328C3F4AAE80F5DF');
        $this->addSql('DROP INDEX IDX_328C3F4AAE80F5DF ON lucca_minute_updating');
        $this->addSql('ALTER TABLE lucca_minute_updating DROP department_id');
        $this->addSql('ALTER TABLE lucca_parameter_service DROP FOREIGN KEY FK_714CCD5AAE80F5DF');
        $this->addSql('DROP INDEX IDX_714CCD5AAE80F5DF ON lucca_parameter_service');
        $this->addSql('ALTER TABLE lucca_parameter_service DROP department_id');
        $this->addSql('ALTER TABLE lucca_parameter_tribunal DROP FOREIGN KEY FK_3F7FE0EAAE80F5DF');
        $this->addSql('DROP INDEX IDX_3F7FE0EAAE80F5DF ON lucca_parameter_tribunal');
        $this->addSql('ALTER TABLE lucca_parameter_tribunal DROP department_id');
        $this->addSql('ALTER TABLE lucca_tag DROP FOREIGN KEY FK_4724FDFEAE80F5DF');
        $this->addSql('DROP INDEX IDX_4724FDFEAE80F5DF ON lucca_tag');
        $this->addSql('ALTER TABLE lucca_tag DROP department_id');
        $this->addSql('ALTER TABLE lucca_parameter_town DROP FOREIGN KEY FK_E7FAE590AE80F5DF');
        $this->addSql('DROP INDEX IDX_E7FAE590AE80F5DF ON lucca_parameter_town');
        $this->addSql('ALTER TABLE lucca_parameter_town DROP department_id');
        $this->addSql('ALTER TABLE lucca_minute_closure DROP FOREIGN KEY FK_99C56459AE80F5DF');
        $this->addSql('DROP INDEX IDX_99C56459AE80F5DF ON lucca_minute_closure');
        $this->addSql('ALTER TABLE lucca_minute_closure DROP department_id');
        $this->addSql('ALTER TABLE lucca_content_subarea DROP FOREIGN KEY FK_80F3844BAE80F5DF');
        $this->addSql('DROP INDEX IDX_80F3844BAE80F5DF ON lucca_content_subarea');
        $this->addSql('ALTER TABLE lucca_content_subarea DROP department_id');
        $this->addSql('ALTER TABLE lucca_proposal DROP FOREIGN KEY FK_FBB4C67AE80F5DF');
        $this->addSql('DROP INDEX IDX_FBB4C67AE80F5DF ON lucca_proposal');
        $this->addSql('ALTER TABLE lucca_proposal DROP department_id');
        $this->addSql('ALTER TABLE lucca_natinf DROP FOREIGN KEY FK_254257C9AE80F5DF');
        $this->addSql('DROP INDEX IDX_254257C9AE80F5DF ON lucca_natinf');
        $this->addSql('ALTER TABLE lucca_natinf DROP department_id');
        $this->addSql('ALTER TABLE lucca_minute DROP FOREIGN KEY FK_901A2828AE80F5DF');
        $this->addSql('DROP INDEX IDX_901A2828AE80F5DF ON lucca_minute');
        $this->addSql('ALTER TABLE lucca_minute DROP department_id');
        $this->addSql('ALTER TABLE lucca_minute_profession DROP FOREIGN KEY FK_AF63ACB9AE80F5DF');
        $this->addSql('DROP INDEX IDX_AF63ACB9AE80F5DF ON lucca_minute_profession');
        $this->addSql('ALTER TABLE lucca_minute_profession DROP department_id');
        $this->addSql('ALTER TABLE lucca_model_margin DROP FOREIGN KEY FK_1B82EE35AE80F5DF');
        $this->addSql('DROP INDEX IDX_1B82EE35AE80F5DF ON lucca_model_margin');
        $this->addSql('ALTER TABLE lucca_model_margin DROP department_id');
        $this->addSql('ALTER TABLE lucca_adherent DROP FOREIGN KEY FK_208D2875AE80F5DF');
        $this->addSql('DROP INDEX IDX_208D2875AE80F5DF ON lucca_adherent');
        $this->addSql('ALTER TABLE lucca_adherent DROP department_id');
        $this->addSql('ALTER TABLE lucca_model DROP FOREIGN KEY FK_DB0D9941AE80F5DF');
        $this->addSql('DROP INDEX IDX_DB0D9941AE80F5DF ON lucca_model');
        $this->addSql('ALTER TABLE lucca_model DROP department_id');

        $this->addSql('ALTER TABLE lucca_minute_folder CHANGE nature nature VARCHAR(30) DEFAULT NULL');
        $this->addSql('ALTER TABLE lucca_model CHANGE privacy privacy LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE lucca_minute_plot CHANGE latitude latitude VARCHAR(20) DEFAULT NULL, CHANGE longitude longitude VARCHAR(20) DEFAULT NULL');
    }
}
