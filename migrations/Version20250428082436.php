<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250428082436 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_adherent (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, firstname VARCHAR(50) NOT NULL, function VARCHAR(50) NOT NULL, address VARCHAR(60) DEFAULT NULL, zipcode VARCHAR(10) DEFAULT NULL, city VARCHAR(50) DEFAULT NULL, phone VARCHAR(15) DEFAULT NULL, mobile VARCHAR(15) DEFAULT NULL, invitedByMail TINYINT(1) NOT NULL, unitAttachment VARCHAR(120) DEFAULT NULL, emailPublic VARCHAR(120) DEFAULT NULL, enabled TINYINT(1) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, user_id INT NOT NULL, town_id INT DEFAULT NULL, intercommunal_id INT DEFAULT NULL, service_id INT DEFAULT NULL, logo_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_208D2875A76ED395 (user_id), INDEX IDX_208D287575E23604 (town_id), INDEX IDX_208D287540B6387D (intercommunal_id), INDEX IDX_208D2875ED5CA9E6 (service_id), INDEX IDX_208D2875F98F144A (logo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_adherent_linked_department (adherent_id INT NOT NULL, department_id INT NOT NULL, INDEX IDX_AE2F95D525F06C53 (adherent_id), INDEX IDX_AE2F95D5AE80F5DF (department_id), PRIMARY KEY(adherent_id, department_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_adherent_agent (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, firstname VARCHAR(50) NOT NULL, function VARCHAR(50) NOT NULL, commission VARCHAR(25) DEFAULT NULL, enabled TINYINT(1) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, department_id INT DEFAULT NULL, tribunal_id INT DEFAULT NULL, adherent_id INT NOT NULL, INDEX IDX_2193A0B0AE80F5DF (department_id), INDEX IDX_2193A0B075C2CEC3 (tribunal_id), INDEX IDX_2193A0B025F06C53 (adherent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_checklist (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, status VARCHAR(30) DEFAULT NULL, description LONGTEXT DEFAULT NULL, enabled TINYINT(1) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, department_id INT DEFAULT NULL, INDEX IDX_3104D71CAE80F5DF (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_checklist_element (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, position SMALLINT NOT NULL, enabled TINYINT(1) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, checklist_id INT NOT NULL, INDEX IDX_77A875CBB16D08A7 (checklist_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_content_area (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, position VARCHAR(30) NOT NULL, enabled TINYINT(1) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, department_id INT DEFAULT NULL, INDEX IDX_11F2D0DEAE80F5DF (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_content_page (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, slug VARCHAR(100) NOT NULL, icon VARCHAR(40) DEFAULT NULL, link VARCHAR(50) NOT NULL, position SMALLINT NOT NULL, content LONGTEXT DEFAULT NULL, enabled TINYINT(1) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, subarea_id INT NOT NULL, author_id INT NOT NULL, department_id INT DEFAULT NULL, INDEX IDX_D26C5B96F66DE853 (subarea_id), INDEX IDX_D26C5B96F675F31B (author_id), INDEX IDX_D26C5B96AE80F5DF (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_content_page_linked_media (page_id INT NOT NULL, media_id INT NOT NULL, INDEX IDX_7838D5CBC4663E4 (page_id), INDEX IDX_7838D5CBEA9FDD75 (media_id), PRIMARY KEY(page_id, media_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_content_subarea (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, code VARCHAR(60) DEFAULT NULL, position SMALLINT NOT NULL, width VARCHAR(100) NOT NULL, color VARCHAR(20) NOT NULL, title VARCHAR(50) NOT NULL, enabled TINYINT(1) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, area_id INT NOT NULL, department_id INT DEFAULT NULL, INDEX IDX_80F3844BBD0F409C (area_id), INDEX IDX_80F3844BAE80F5DF (department_id), UNIQUE INDEX UNIQ_80F3844B77153098AE80F5DF (code, department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_department (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, domainName VARCHAR(255) NOT NULL, comment LONGTEXT DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, enabled TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_72A0ED2677153098 (code), UNIQUE INDEX UNIQ_72A0ED268DFE9A8 (domainName), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_log (id INT AUTO_INCREMENT NOT NULL, objectId INT DEFAULT NULL, classname VARCHAR(255) NOT NULL, status VARCHAR(20) NOT NULL, createdAt DATETIME NOT NULL, shortMessage VARCHAR(255) NOT NULL, message LONGTEXT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_CB9222B8A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_media (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, nameOriginal VARCHAR(255) NOT NULL, nameCanonical VARCHAR(255) NOT NULL, public TINYINT(1) NOT NULL, filePath VARCHAR(255) DEFAULT NULL, copyright VARCHAR(150) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, mimeType VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, category_id INT NOT NULL, folder_id INT NOT NULL, owner_id INT NOT NULL, INDEX IDX_66B44A9412469DE2 (category_id), INDEX IDX_66B44A94162CB942 (folder_id), INDEX IDX_66B44A947E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_media_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, icon VARCHAR(50) NOT NULL, description LONGTEXT DEFAULT NULL, enabled TINYINT(1) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, storager_id INT NOT NULL, INDEX IDX_D3F1642AF9E8CDF (storager_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_media_category_linked_meta_data_model (category_id INT NOT NULL, meta_data_model_id INT NOT NULL, INDEX IDX_37E2319412469DE2 (category_id), INDEX IDX_37E23194C9E25533 (meta_data_model_id), PRIMARY KEY(category_id, meta_data_model_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_media_category_linked_extension (category_id INT NOT NULL, extension_id INT NOT NULL, INDEX IDX_336D379612469DE2 (category_id), INDEX IDX_336D3796812D5EB (extension_id), PRIMARY KEY(category_id, extension_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_media_extension (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, value VARCHAR(50) NOT NULL, description LONGTEXT DEFAULT NULL, enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_media_folder (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, nameCanonical VARCHAR(150) NOT NULL, path VARCHAR(150) NOT NULL, virtualPath VARCHAR(150) DEFAULT NULL, public TINYINT(1) NOT NULL, description LONGTEXT DEFAULT NULL, enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_media_gallery (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, description LONGTEXT DEFAULT NULL, enabled TINYINT(1) NOT NULL, default_media_id INT DEFAULT NULL, INDEX IDX_EFFBDAF5ECDBED3B (default_media_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_media_gallery_linked_media (gallery_id INT NOT NULL, media_id INT NOT NULL, INDEX IDX_5D42AD8B4E7AF8F (gallery_id), INDEX IDX_5D42AD8BEA9FDD75 (media_id), PRIMARY KEY(gallery_id, media_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_media_meta_data (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, keyword VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, media_id INT DEFAULT NULL, INDEX IDX_4AEF21B5EA9FDD75 (media_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_media_meta_data_model (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, keyword VARCHAR(50) NOT NULL, description LONGTEXT DEFAULT NULL, enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_media_storager (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, serviceFolderNaming VARCHAR(50) NOT NULL, serviceMediaNaming VARCHAR(50) NOT NULL, description LONGTEXT DEFAULT NULL, enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_media_storager_linked_folder (storager_id INT NOT NULL, folder_id INT NOT NULL, INDEX IDX_B0394800AF9E8CDF (storager_id), INDEX IDX_B0394800162CB942 (folder_id), PRIMARY KEY(storager_id, folder_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute (id INT AUTO_INCREMENT NOT NULL, num VARCHAR(20) NOT NULL, status VARCHAR(50) DEFAULT NULL, dateOpening DATETIME NOT NULL, dateLastUpdate DATETIME DEFAULT NULL, dateComplaint DATETIME DEFAULT NULL, nameComplaint VARCHAR(60) DEFAULT NULL, isClosed TINYINT(1) NOT NULL, reporting LONGTEXT DEFAULT NULL, origin VARCHAR(30) DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, adherent_id INT NOT NULL, plot_id INT NOT NULL, department_id INT DEFAULT NULL, tribunal_id INT DEFAULT NULL, tribunal_competent_id INT DEFAULT NULL, agent_id INT DEFAULT NULL, closure_id INT DEFAULT NULL, INDEX IDX_901A282825F06C53 (adherent_id), UNIQUE INDEX UNIQ_901A2828680D0B01 (plot_id), INDEX IDX_901A2828AE80F5DF (department_id), INDEX IDX_901A282875C2CEC3 (tribunal_id), INDEX IDX_901A28281832BF33 (tribunal_competent_id), INDEX IDX_901A28283414710B (agent_id), UNIQUE INDEX UNIQ_901A2828FA43E9DA (closure_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_linked_human (minute_id INT NOT NULL, human_id INT NOT NULL, INDEX IDX_358F79939D4D3F1B (minute_id), INDEX IDX_358F79938ABD4580 (human_id), PRIMARY KEY(minute_id, human_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_agent_attendant (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, firstname VARCHAR(50) NOT NULL, function VARCHAR(50) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_closure (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(35) NOT NULL, dateClosing DATETIME NOT NULL, natureRegularized VARCHAR(50) DEFAULT NULL, initiatingStructure VARCHAR(50) DEFAULT NULL, reason VARCHAR(255) DEFAULT NULL, observation LONGTEXT DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, minute_id INT NOT NULL, department_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_99C564599D4D3F1B (minute_id), INDEX IDX_99C56459AE80F5DF (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_commission (id INT AUTO_INCREMENT NOT NULL, dateHearing DATETIME DEFAULT NULL, dateAdjournment DATETIME DEFAULT NULL, dateDeliberation DATETIME DEFAULT NULL, amountFine INT DEFAULT NULL, dateJudicialDesision DATETIME DEFAULT NULL, statusDecision VARCHAR(40) DEFAULT NULL, amountPenalty INT DEFAULT NULL, dateExecution DATETIME DEFAULT NULL, restitution LONGTEXT DEFAULT NULL, dateStartPenality DATETIME DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_contradictory (id INT AUTO_INCREMENT NOT NULL, dateNoticeDdtm DATETIME DEFAULT NULL, dateExecution DATETIME DEFAULT NULL, dateAnswer DATETIME DEFAULT NULL, answer LONGTEXT DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_control (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(25) NOT NULL, datePostal DATETIME DEFAULT NULL, dateSended DATETIME DEFAULT NULL, dateNotified DATETIME DEFAULT NULL, dateReturned DATETIME DEFAULT NULL, reason VARCHAR(60) DEFAULT NULL, dateContact DATETIME DEFAULT NULL, accepted VARCHAR(40) DEFAULT NULL, dateControl DATETIME DEFAULT NULL, hourControl TIME DEFAULT NULL, stateControl VARCHAR(60) NOT NULL, summoned TINYINT(1) DEFAULT NULL, courierDelivery VARCHAR(50) DEFAULT NULL, isFenced TINYINT(1) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, minute_id INT NOT NULL, agent_id INT NOT NULL, department_id INT DEFAULT NULL, folder_id INT DEFAULT NULL, INDEX IDX_3C19C5C39D4D3F1B (minute_id), INDEX IDX_3C19C5C33414710B (agent_id), INDEX IDX_3C19C5C3AE80F5DF (department_id), UNIQUE INDEX UNIQ_3C19C5C3162CB942 (folder_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_control_linked_human_minute (control_id INT NOT NULL, human_id INT NOT NULL, INDEX IDX_CA3FE67532BEC70E (control_id), INDEX IDX_CA3FE6758ABD4580 (human_id), PRIMARY KEY(control_id, human_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_control_linked_human_control (control_id INT NOT NULL, human_id INT NOT NULL, INDEX IDX_761B13B632BEC70E (control_id), INDEX IDX_761B13B68ABD4580 (human_id), PRIMARY KEY(control_id, human_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_control_linked_agent_attendant (control_id INT NOT NULL, agent_attendant_id INT NOT NULL, INDEX IDX_3044996132BEC70E (control_id), INDEX IDX_304499613BAC4DFE (agent_attendant_id), PRIMARY KEY(control_id, agent_attendant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_control_edition (id INT AUTO_INCREMENT NOT NULL, convocationEdited TINYINT(1) NOT NULL, letterConvocation LONGTEXT DEFAULT NULL, accessEdited TINYINT(1) NOT NULL, letterAccess LONGTEXT DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, control_id INT NOT NULL, human_id INT NOT NULL, INDEX IDX_F6DD0F4E32BEC70E (control_id), INDEX IDX_F6DD0F4E8ABD4580 (human_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_courier (id INT AUTO_INCREMENT NOT NULL, dateOffender DATETIME DEFAULT NULL, dateJudicial DATETIME DEFAULT NULL, context LONGTEXT DEFAULT NULL, civilParty TINYINT(1) DEFAULT NULL, amount INT DEFAULT NULL, dateDdtm DATETIME DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, folder_id INT NOT NULL, department_id INT DEFAULT NULL, edition_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_1ED1A5F4162CB942 (folder_id), INDEX IDX_1ED1A5F4AE80F5DF (department_id), UNIQUE INDEX UNIQ_1ED1A5F474281A5E (edition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_courier_edition (id INT AUTO_INCREMENT NOT NULL, judicialEdited TINYINT(1) NOT NULL, letterJudicial LONGTEXT DEFAULT NULL, ddtmEdited TINYINT(1) NOT NULL, letterDdtm LONGTEXT DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_courier_human_edition (id INT AUTO_INCREMENT NOT NULL, letterOffenderEdited TINYINT(1) NOT NULL, letterOffender LONGTEXT DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, courier_id INT NOT NULL, human_id INT NOT NULL, INDEX IDX_8510A934E3D8151C (courier_id), INDEX IDX_8510A9348ABD4580 (human_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_decision (id INT AUTO_INCREMENT NOT NULL, appeal TINYINT(1) NOT NULL, cassationComplaint TINYINT(1) DEFAULT NULL, dateAskCassation DATETIME DEFAULT NULL, dateAnswerCassation DATETIME DEFAULT NULL, statusCassation TINYINT(1) DEFAULT NULL, nameNewCassation VARCHAR(35) DEFAULT NULL, dateReferralEurope DATETIME DEFAULT NULL, answerEurope DATETIME DEFAULT NULL, dataEurope LONGTEXT DEFAULT NULL, amountPenaltyDaily INT DEFAULT NULL, dateStartRecovery DATETIME DEFAULT NULL, dateNoticeDdtm DATETIME DEFAULT NULL, totalPenaltyRecovery INT DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, minute_id INT NOT NULL, tribunal_id INT DEFAULT NULL, tribunal_commission_id INT DEFAULT NULL, department_id INT DEFAULT NULL, appeal_commission_id INT DEFAULT NULL, cassation_comission_id INT DEFAULT NULL, INDEX IDX_671E77B39D4D3F1B (minute_id), INDEX IDX_671E77B375C2CEC3 (tribunal_id), INDEX IDX_671E77B3CA50AE44 (tribunal_commission_id), INDEX IDX_671E77B3AE80F5DF (department_id), INDEX IDX_671E77B3BD7F0960 (appeal_commission_id), INDEX IDX_671E77B3D193C759 (cassation_comission_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_decision_linked_penalty (decision_id INT NOT NULL, penalty_id INT NOT NULL, INDEX IDX_1607E7BBBDEE7539 (decision_id), INDEX IDX_1607E7BB17C4A6C7 (penalty_id), PRIMARY KEY(decision_id, penalty_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_decision_linked_liquidation (decision_id INT NOT NULL, liquidation_id INT NOT NULL, INDEX IDX_231DFAEBBDEE7539 (decision_id), INDEX IDX_231DFAEB90140D4C (liquidation_id), PRIMARY KEY(decision_id, liquidation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_decision_linked_penalty_appeal (decision_id INT NOT NULL, penalty_appeal_id INT NOT NULL, INDEX IDX_812B40E1BDEE7539 (decision_id), INDEX IDX_812B40E1311274DA (penalty_appeal_id), PRIMARY KEY(decision_id, penalty_appeal_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_decision_linked_contradictory (decision_id INT NOT NULL, contradictory_id INT NOT NULL, INDEX IDX_C805B7EFBDEE7539 (decision_id), INDEX IDX_C805B7EF15AC53F7 (contradictory_id), PRIMARY KEY(decision_id, contradictory_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_demolition (id INT AUTO_INCREMENT NOT NULL, company VARCHAR(50) DEFAULT NULL, amountCompany INT DEFAULT NULL, dateDemolition DATETIME DEFAULT NULL, bailif VARCHAR(50) DEFAULT NULL, amountBailif INT DEFAULT NULL, comment LONGTEXT DEFAULT NULL, result VARCHAR(30) DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, decision_id INT NOT NULL, UNIQUE INDEX UNIQ_EB2066FBDEE7539 (decision_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_demolition_linked_profession (demolition_id INT NOT NULL, profession_id INT NOT NULL, INDEX IDX_90442EFFD1D77694 (demolition_id), INDEX IDX_90442EFFFDEF8996 (profession_id), PRIMARY KEY(demolition_id, profession_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_expulsion (id INT AUTO_INCREMENT NOT NULL, lawFirm VARCHAR(50) DEFAULT NULL, amountDelivrery INT DEFAULT NULL, dateHearing DATETIME DEFAULT NULL, dateAdjournment DATETIME DEFAULT NULL, dateDeliberation DATETIME DEFAULT NULL, dateJudicialDesision DATETIME DEFAULT NULL, statusDecision VARCHAR(50) DEFAULT NULL, comment LONGTEXT DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, decision_id INT NOT NULL, UNIQUE INDEX UNIQ_3862D1C2BDEE7539 (decision_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_folder (id INT AUTO_INCREMENT NOT NULL, num VARCHAR(25) NOT NULL, type VARCHAR(30) DEFAULT NULL, nature VARCHAR(100) DEFAULT NULL, reasonObstacle VARCHAR(50) DEFAULT NULL, dateClosure DATETIME DEFAULT NULL, ascertainment LONGTEXT DEFAULT NULL, details LONGTEXT DEFAULT NULL, violation LONGTEXT DEFAULT NULL, isReReaded TINYINT(1) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, minute_id INT NOT NULL, control_id INT NOT NULL, department_id INT DEFAULT NULL, courier_id INT DEFAULT NULL, edition_id INT DEFAULT NULL, folder_signed_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_952C7069DC43AF6E (num), INDEX IDX_952C70699D4D3F1B (minute_id), UNIQUE INDEX UNIQ_952C706932BEC70E (control_id), INDEX IDX_952C7069AE80F5DF (department_id), UNIQUE INDEX UNIQ_952C7069E3D8151C (courier_id), UNIQUE INDEX UNIQ_952C706974281A5E (edition_id), INDEX IDX_952C7069A0EC76E1 (folder_signed_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_folder_linked_natinf (folder_id INT NOT NULL, natinf_id INT NOT NULL, INDEX IDX_7AA730A8162CB942 (folder_id), INDEX IDX_7AA730A8F87A39C6 (natinf_id), PRIMARY KEY(folder_id, natinf_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_folder_linked_tag_nature (folder_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_F1FBC128162CB942 (folder_id), INDEX IDX_F1FBC128BAD26311 (tag_id), PRIMARY KEY(folder_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_folder_linked_tag_town (folder_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_BD8751FF162CB942 (folder_id), INDEX IDX_BD8751FFBAD26311 (tag_id), PRIMARY KEY(folder_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_folder_linked_human_minute (folder_id INT NOT NULL, human_id INT NOT NULL, INDEX IDX_1FFCB698162CB942 (folder_id), INDEX IDX_1FFCB6988ABD4580 (human_id), PRIMARY KEY(folder_id, human_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_folder_linked_human_folder (folder_id INT NOT NULL, human_id INT NOT NULL, INDEX IDX_9D9C14E0162CB942 (folder_id), INDEX IDX_9D9C14E08ABD4580 (human_id), PRIMARY KEY(folder_id, human_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_folder_linked_media (page_id INT NOT NULL, media_id INT NOT NULL, INDEX IDX_3CDFC700C4663E4 (page_id), INDEX IDX_3CDFC700EA9FDD75 (media_id), PRIMARY KEY(page_id, media_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_folder_edition (id INT AUTO_INCREMENT NOT NULL, folderEdited TINYINT(1) NOT NULL, folderVersion LONGTEXT DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_folder_element (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, state TINYINT(1) NOT NULL, position SMALLINT DEFAULT NULL, comment LONGTEXT DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, folder_id INT NOT NULL, image_id INT DEFAULT NULL, INDEX IDX_7F4CB9D2162CB942 (folder_id), INDEX IDX_7F4CB9D23DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_human (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, firstname VARCHAR(50) NOT NULL, status VARCHAR(30) NOT NULL, gender VARCHAR(30) NOT NULL, person VARCHAR(30) NOT NULL, address LONGTEXT DEFAULT NULL, company VARCHAR(50) DEFAULT NULL, addressCompany LONGTEXT DEFAULT NULL, statusCompany VARCHAR(30) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, department_id INT DEFAULT NULL, INDEX IDX_F7A3D5FCAE80F5DF (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_liquidation (id INT AUTO_INCREMENT NOT NULL, dateStart DATETIME DEFAULT NULL, dateEnd DATETIME DEFAULT NULL, amountPenalty INT DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_mayor_letter (id INT AUTO_INCREMENT NOT NULL, gender VARCHAR(30) NOT NULL, name VARCHAR(150) NOT NULL, address VARCHAR(255) NOT NULL, dateSended DATETIME DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, department_id INT DEFAULT NULL, town_id INT NOT NULL, adherent_id INT NOT NULL, agent_id INT DEFAULT NULL, INDEX IDX_F13501DDAE80F5DF (department_id), INDEX IDX_F13501DD75E23604 (town_id), INDEX IDX_F13501DD25F06C53 (adherent_id), INDEX IDX_F13501DD3414710B (agent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_mayor_letter_linked_folder (mayor_letter_id INT NOT NULL, folder_id INT NOT NULL, INDEX IDX_F7480D3C52EB023D (mayor_letter_id), INDEX IDX_F7480D3C162CB942 (folder_id), PRIMARY KEY(mayor_letter_id, folder_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_minute_story (id INT AUTO_INCREMENT NOT NULL, dateUpdate DATETIME NOT NULL, status VARCHAR(50) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, minute_id INT NOT NULL, updating_by_id INT NOT NULL, department_id INT DEFAULT NULL, INDEX IDX_927A47D39D4D3F1B (minute_id), INDEX IDX_927A47D3AA827920 (updating_by_id), INDEX IDX_927A47D3AE80F5DF (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_penalty (id INT AUTO_INCREMENT NOT NULL, dateFolder DATETIME DEFAULT NULL, preparedBy VARCHAR(50) NOT NULL, nature VARCHAR(30) DEFAULT NULL, amountPenalty INT DEFAULT NULL, dateStart DATETIME DEFAULT NULL, dateEnd DATETIME DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_penalty_appeal (id INT AUTO_INCREMENT NOT NULL, juridiction VARCHAR(50) NOT NULL, dateDecision DATETIME DEFAULT NULL, kindDecision VARCHAR(50) DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_plot (id INT AUTO_INCREMENT NOT NULL, parcel VARCHAR(50) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, isRiskZone TINYINT(1) DEFAULT NULL, risk VARCHAR(50) DEFAULT NULL, place VARCHAR(50) DEFAULT NULL, latitude NUMERIC(40, 30) DEFAULT NULL, longitude NUMERIC(40, 30) DEFAULT NULL, locationFrom VARCHAR(50) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, town_id INT NOT NULL, department_id INT DEFAULT NULL, INDEX IDX_1759B03A75E23604 (town_id), INDEX IDX_1759B03AAE80F5DF (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_profession (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, company VARCHAR(50) NOT NULL, amountActivity INT DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, department_id INT DEFAULT NULL, INDEX IDX_AF63ACB9AE80F5DF (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_updating (id INT AUTO_INCREMENT NOT NULL, num VARCHAR(25) NOT NULL, nature VARCHAR(30) DEFAULT NULL, description LONGTEXT DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, minute_id INT NOT NULL, department_id INT DEFAULT NULL, INDEX IDX_328C3F4A9D4D3F1B (minute_id), INDEX IDX_328C3F4AAE80F5DF (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_minute_updating_linked_control (updating_id INT NOT NULL, control_id INT NOT NULL, INDEX IDX_C89E8B0D215A0A56 (updating_id), INDEX IDX_C89E8B0D32BEC70E (control_id), PRIMARY KEY(updating_id, control_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_model (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, size VARCHAR(50) NOT NULL, orientation VARCHAR(50) NOT NULL, type VARCHAR(50) NOT NULL, layout VARCHAR(50) NOT NULL, documents JSON NOT NULL, color VARCHAR(20) DEFAULT NULL, background VARCHAR(20) DEFAULT NULL, font VARCHAR(50) DEFAULT 'choice.font.marianne.regular' NOT NULL, enabled TINYINT(1) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, recto_id INT NOT NULL, department_id INT DEFAULT NULL, verso_id INT DEFAULT NULL, owner_id INT DEFAULT NULL, shared_service_id INT DEFAULT NULL, shared_intercommunal_id INT DEFAULT NULL, shared_town_id INT DEFAULT NULL, INDEX IDX_DB0D99412AAEEA4A (recto_id), INDEX IDX_DB0D9941AE80F5DF (department_id), INDEX IDX_DB0D9941D44EE99E (verso_id), INDEX IDX_DB0D99417E3C61F9 (owner_id), INDEX IDX_DB0D9941994F4929 (shared_service_id), INDEX IDX_DB0D994199886B3E (shared_intercommunal_id), INDEX IDX_DB0D994154353AB3 (shared_town_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_model_bloc (id INT AUTO_INCREMENT NOT NULL, height INT NOT NULL, width INT NOT NULL, type_content VARCHAR(50) NOT NULL, header_size SMALLINT DEFAULT NULL, footer_size SMALLINT DEFAULT NULL, left_size SMALLINT DEFAULT NULL, right_size SMALLINT DEFAULT NULL, content LONGTEXT DEFAULT NULL, color VARCHAR(20) DEFAULT NULL, css_inline LONGTEXT DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, margin_id INT NOT NULL, department_id INT DEFAULT NULL, media_id INT DEFAULT NULL, background_img_id INT DEFAULT NULL, INDEX IDX_5592B511EFB9A5B6 (margin_id), INDEX IDX_5592B511AE80F5DF (department_id), INDEX IDX_5592B511EA9FDD75 (media_id), INDEX IDX_5592B51165310DAC (background_img_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_model_margin (id INT AUTO_INCREMENT NOT NULL, position VARCHAR(50) NOT NULL, height INT DEFAULT NULL, width INT DEFAULT NULL, background VARCHAR(20) DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, department_id INT DEFAULT NULL, background_img_id INT DEFAULT NULL, INDEX IDX_1B82EE35AE80F5DF (department_id), INDEX IDX_1B82EE3565310DAC (background_img_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_model_page (id INT AUTO_INCREMENT NOT NULL, marginUnit VARCHAR(50) DEFAULT NULL, font VARCHAR(50) DEFAULT NULL, color VARCHAR(20) DEFAULT NULL, background VARCHAR(20) DEFAULT NULL, css_inline LONGTEXT DEFAULT NULL, header_size SMALLINT DEFAULT NULL, footer_size SMALLINT DEFAULT NULL, left_size SMALLINT DEFAULT NULL, right_size SMALLINT DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, department_id INT DEFAULT NULL, margin_top_id INT DEFAULT NULL, margin_bottom_id INT DEFAULT NULL, margin_left_id INT DEFAULT NULL, margin_right_id INT DEFAULT NULL, INDEX IDX_86E0966BAE80F5DF (department_id), UNIQUE INDEX UNIQ_86E0966BF9FB2723 (margin_top_id), UNIQUE INDEX UNIQ_86E0966B1856F4B6 (margin_bottom_id), UNIQUE INDEX UNIQ_86E0966BC2329C24 (margin_left_id), UNIQUE INDEX UNIQ_86E0966B86BAB39A (margin_right_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_natinf (id INT AUTO_INCREMENT NOT NULL, num INT NOT NULL, qualification VARCHAR(255) NOT NULL, definedBy VARCHAR(255) NOT NULL, repressedBy VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, department_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, INDEX IDX_254257C9AE80F5DF (department_id), INDEX IDX_254257C9727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_natinf_linked_tag (natinf_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_6CF917EFF87A39C6 (natinf_id), INDEX IDX_6CF917EFBAD26311 (tag_id), PRIMARY KEY(natinf_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_parameter_intercommunal (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(20) NOT NULL, name VARCHAR(70) NOT NULL, enabled TINYINT(1) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, department_id INT DEFAULT NULL, office_id INT DEFAULT NULL, INDEX IDX_2B223A6BAE80F5DF (department_id), INDEX IDX_2B223A6BFFA0C224 (office_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_parameter_partner (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(20) NOT NULL, name VARCHAR(150) NOT NULL, enabled TINYINT(1) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, department_id INT DEFAULT NULL, INDEX IDX_A1FA699EAE80F5DF (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_parameter_service (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(20) NOT NULL, name VARCHAR(100) NOT NULL, enabled TINYINT(1) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, department_id INT DEFAULT NULL, office_id INT DEFAULT NULL, INDEX IDX_714CCD5AAE80F5DF (department_id), INDEX IDX_714CCD5AFFA0C224 (office_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_parameter_town (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(20) NOT NULL, name VARCHAR(50) NOT NULL, office VARCHAR(50) NOT NULL, zipcode VARCHAR(50) NOT NULL, enabled TINYINT(1) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, department_id INT DEFAULT NULL, intercommunal_id INT DEFAULT NULL, INDEX IDX_E7FAE590AE80F5DF (department_id), INDEX IDX_E7FAE59040B6387D (intercommunal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_parameter_tribunal (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(20) NOT NULL, name VARCHAR(50) NOT NULL, interlocutor VARCHAR(80) DEFAULT NULL, address VARCHAR(80) DEFAULT NULL, addressCpl VARCHAR(80) DEFAULT NULL, zipCode VARCHAR(10) DEFAULT NULL, city VARCHAR(50) DEFAULT NULL, region VARCHAR(50) DEFAULT NULL, country VARCHAR(50) DEFAULT NULL, enabled TINYINT(1) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, department_id INT DEFAULT NULL, office_id INT DEFAULT NULL, INDEX IDX_3F7FE0EAAE80F5DF (department_id), INDEX IDX_3F7FE0EAFFA0C224 (office_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_proposal (id INT AUTO_INCREMENT NOT NULL, sentence LONGTEXT NOT NULL, enabled TINYINT(1) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, tag_id INT NOT NULL, department_id INT DEFAULT NULL, INDEX IDX_FBB4C67BAD26311 (tag_id), INDEX IDX_FBB4C67AE80F5DF (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_security_login_attempt (id INT AUTO_INCREMENT NOT NULL, requestIp VARCHAR(50) NOT NULL, clientIps VARCHAR(255) DEFAULT NULL, requestUri VARCHAR(250) NOT NULL, requestedAt DATETIME NOT NULL, username VARCHAR(100) NOT NULL, controllerAsked VARCHAR(255) DEFAULT NULL, firewall VARCHAR(255) DEFAULT NULL, agent VARCHAR(255) DEFAULT NULL, host VARCHAR(255) DEFAULT NULL, system VARCHAR(255) DEFAULT NULL, software VARCHAR(255) DEFAULT NULL, address VARCHAR(255) NOT NULL, port VARCHAR(30) NOT NULL, addressRemote VARCHAR(255) NOT NULL, portRemote VARCHAR(30) NOT NULL, requestTime VARCHAR(200) NOT NULL, isCleared TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_setting (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, accessType VARCHAR(255) NOT NULL, position SMALLINT NOT NULL, value LONGTEXT DEFAULT NULL, valuesAvailable LONGTEXT DEFAULT NULL, comment LONGTEXT DEFAULT NULL, category_id INT NOT NULL, department_id INT DEFAULT NULL, INDEX IDX_11348FE212469DE2 (category_id), INDEX IDX_11348FE2AE80F5DF (department_id), UNIQUE INDEX UNIQ_11348FE25E237E06AE80F5DF (name, department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_setting_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, icon VARCHAR(255) NOT NULL, position SMALLINT NOT NULL, comment LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_tag (id INT AUTO_INCREMENT NOT NULL, num SMALLINT NOT NULL, name VARCHAR(50) NOT NULL, category VARCHAR(30) DEFAULT NULL, description LONGTEXT DEFAULT NULL, enabled TINYINT(1) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, department_id INT DEFAULT NULL, INDEX IDX_4724FDFEAE80F5DF (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) DEFAULT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT '(DC2Type:array)', UNIQUE INDEX UNIQ_A363768292FC23A8 (username_canonical), UNIQUE INDEX UNIQ_A3637682A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_A3637682C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_user_linked_group (user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_7873C38A76ED395 (user_id), INDEX IDX_7873C38FE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lucca_user_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT '(DC2Type:array)', displayed TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_30BFB3315E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent ADD CONSTRAINT FK_208D2875A76ED395 FOREIGN KEY (user_id) REFERENCES lucca_user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent ADD CONSTRAINT FK_208D287575E23604 FOREIGN KEY (town_id) REFERENCES lucca_parameter_town (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent ADD CONSTRAINT FK_208D287540B6387D FOREIGN KEY (intercommunal_id) REFERENCES lucca_parameter_intercommunal (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent ADD CONSTRAINT FK_208D2875ED5CA9E6 FOREIGN KEY (service_id) REFERENCES lucca_parameter_service (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent ADD CONSTRAINT FK_208D2875F98F144A FOREIGN KEY (logo_id) REFERENCES lucca_media (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent_linked_department ADD CONSTRAINT FK_AE2F95D525F06C53 FOREIGN KEY (adherent_id) REFERENCES lucca_adherent (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent_linked_department ADD CONSTRAINT FK_AE2F95D5AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent_agent ADD CONSTRAINT FK_2193A0B0AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent_agent ADD CONSTRAINT FK_2193A0B075C2CEC3 FOREIGN KEY (tribunal_id) REFERENCES lucca_parameter_tribunal (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent_agent ADD CONSTRAINT FK_2193A0B025F06C53 FOREIGN KEY (adherent_id) REFERENCES lucca_adherent (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_checklist ADD CONSTRAINT FK_3104D71CAE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_checklist_element ADD CONSTRAINT FK_77A875CBB16D08A7 FOREIGN KEY (checklist_id) REFERENCES lucca_checklist (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_content_area ADD CONSTRAINT FK_11F2D0DEAE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_content_page ADD CONSTRAINT FK_D26C5B96F66DE853 FOREIGN KEY (subarea_id) REFERENCES lucca_content_subarea (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_content_page ADD CONSTRAINT FK_D26C5B96F675F31B FOREIGN KEY (author_id) REFERENCES lucca_user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_content_page ADD CONSTRAINT FK_D26C5B96AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_content_page_linked_media ADD CONSTRAINT FK_7838D5CBC4663E4 FOREIGN KEY (page_id) REFERENCES lucca_content_page (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_content_page_linked_media ADD CONSTRAINT FK_7838D5CBEA9FDD75 FOREIGN KEY (media_id) REFERENCES lucca_media (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_content_subarea ADD CONSTRAINT FK_80F3844BBD0F409C FOREIGN KEY (area_id) REFERENCES lucca_content_area (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_content_subarea ADD CONSTRAINT FK_80F3844BAE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_log ADD CONSTRAINT FK_CB9222B8A76ED395 FOREIGN KEY (user_id) REFERENCES lucca_user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_media ADD CONSTRAINT FK_66B44A9412469DE2 FOREIGN KEY (category_id) REFERENCES lucca_media_category (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_media ADD CONSTRAINT FK_66B44A94162CB942 FOREIGN KEY (folder_id) REFERENCES lucca_media_folder (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_media ADD CONSTRAINT FK_66B44A947E3C61F9 FOREIGN KEY (owner_id) REFERENCES lucca_user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_media_category ADD CONSTRAINT FK_D3F1642AF9E8CDF FOREIGN KEY (storager_id) REFERENCES lucca_media_storager (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_media_category_linked_meta_data_model ADD CONSTRAINT FK_37E2319412469DE2 FOREIGN KEY (category_id) REFERENCES lucca_media_category (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_media_category_linked_meta_data_model ADD CONSTRAINT FK_37E23194C9E25533 FOREIGN KEY (meta_data_model_id) REFERENCES lucca_media_meta_data_model (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_media_category_linked_extension ADD CONSTRAINT FK_336D379612469DE2 FOREIGN KEY (category_id) REFERENCES lucca_media_category (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_media_category_linked_extension ADD CONSTRAINT FK_336D3796812D5EB FOREIGN KEY (extension_id) REFERENCES lucca_media_extension (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_media_gallery ADD CONSTRAINT FK_EFFBDAF5ECDBED3B FOREIGN KEY (default_media_id) REFERENCES lucca_media (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_media_gallery_linked_media ADD CONSTRAINT FK_5D42AD8B4E7AF8F FOREIGN KEY (gallery_id) REFERENCES lucca_media_gallery (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_media_gallery_linked_media ADD CONSTRAINT FK_5D42AD8BEA9FDD75 FOREIGN KEY (media_id) REFERENCES lucca_media (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_media_meta_data ADD CONSTRAINT FK_4AEF21B5EA9FDD75 FOREIGN KEY (media_id) REFERENCES lucca_media (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_media_storager_linked_folder ADD CONSTRAINT FK_B0394800AF9E8CDF FOREIGN KEY (storager_id) REFERENCES lucca_media_storager (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_media_storager_linked_folder ADD CONSTRAINT FK_B0394800162CB942 FOREIGN KEY (folder_id) REFERENCES lucca_media_folder (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute ADD CONSTRAINT FK_901A282825F06C53 FOREIGN KEY (adherent_id) REFERENCES lucca_adherent (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute ADD CONSTRAINT FK_901A2828680D0B01 FOREIGN KEY (plot_id) REFERENCES lucca_minute_plot (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute ADD CONSTRAINT FK_901A2828AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute ADD CONSTRAINT FK_901A282875C2CEC3 FOREIGN KEY (tribunal_id) REFERENCES lucca_parameter_tribunal (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute ADD CONSTRAINT FK_901A28281832BF33 FOREIGN KEY (tribunal_competent_id) REFERENCES lucca_parameter_tribunal (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute ADD CONSTRAINT FK_901A28283414710B FOREIGN KEY (agent_id) REFERENCES lucca_adherent_agent (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute ADD CONSTRAINT FK_901A2828FA43E9DA FOREIGN KEY (closure_id) REFERENCES lucca_minute_closure (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_linked_human ADD CONSTRAINT FK_358F79939D4D3F1B FOREIGN KEY (minute_id) REFERENCES lucca_minute (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_linked_human ADD CONSTRAINT FK_358F79938ABD4580 FOREIGN KEY (human_id) REFERENCES lucca_minute_human (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_closure ADD CONSTRAINT FK_99C564599D4D3F1B FOREIGN KEY (minute_id) REFERENCES lucca_minute (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_closure ADD CONSTRAINT FK_99C56459AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_control ADD CONSTRAINT FK_3C19C5C39D4D3F1B FOREIGN KEY (minute_id) REFERENCES lucca_minute (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_control ADD CONSTRAINT FK_3C19C5C33414710B FOREIGN KEY (agent_id) REFERENCES lucca_adherent_agent (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_control ADD CONSTRAINT FK_3C19C5C3AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_control ADD CONSTRAINT FK_3C19C5C3162CB942 FOREIGN KEY (folder_id) REFERENCES lucca_minute_folder (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_control_linked_human_minute ADD CONSTRAINT FK_CA3FE67532BEC70E FOREIGN KEY (control_id) REFERENCES lucca_minute_control (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_control_linked_human_minute ADD CONSTRAINT FK_CA3FE6758ABD4580 FOREIGN KEY (human_id) REFERENCES lucca_minute_human (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_control_linked_human_control ADD CONSTRAINT FK_761B13B632BEC70E FOREIGN KEY (control_id) REFERENCES lucca_minute_control (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_control_linked_human_control ADD CONSTRAINT FK_761B13B68ABD4580 FOREIGN KEY (human_id) REFERENCES lucca_minute_human (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_control_linked_agent_attendant ADD CONSTRAINT FK_3044996132BEC70E FOREIGN KEY (control_id) REFERENCES lucca_minute_control (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_control_linked_agent_attendant ADD CONSTRAINT FK_304499613BAC4DFE FOREIGN KEY (agent_attendant_id) REFERENCES lucca_minute_agent_attendant (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_control_edition ADD CONSTRAINT FK_F6DD0F4E32BEC70E FOREIGN KEY (control_id) REFERENCES lucca_minute_control (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_control_edition ADD CONSTRAINT FK_F6DD0F4E8ABD4580 FOREIGN KEY (human_id) REFERENCES lucca_minute_human (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_courier ADD CONSTRAINT FK_1ED1A5F4162CB942 FOREIGN KEY (folder_id) REFERENCES lucca_minute_folder (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_courier ADD CONSTRAINT FK_1ED1A5F4AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_courier ADD CONSTRAINT FK_1ED1A5F474281A5E FOREIGN KEY (edition_id) REFERENCES lucca_minute_courier_edition (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_courier_human_edition ADD CONSTRAINT FK_8510A934E3D8151C FOREIGN KEY (courier_id) REFERENCES lucca_minute_courier (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_courier_human_edition ADD CONSTRAINT FK_8510A9348ABD4580 FOREIGN KEY (human_id) REFERENCES lucca_minute_human (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_decision ADD CONSTRAINT FK_671E77B39D4D3F1B FOREIGN KEY (minute_id) REFERENCES lucca_minute (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_decision ADD CONSTRAINT FK_671E77B375C2CEC3 FOREIGN KEY (tribunal_id) REFERENCES lucca_parameter_tribunal (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_decision ADD CONSTRAINT FK_671E77B3CA50AE44 FOREIGN KEY (tribunal_commission_id) REFERENCES lucca_minute_commission (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_decision ADD CONSTRAINT FK_671E77B3AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_decision ADD CONSTRAINT FK_671E77B3BD7F0960 FOREIGN KEY (appeal_commission_id) REFERENCES lucca_minute_commission (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_decision ADD CONSTRAINT FK_671E77B3D193C759 FOREIGN KEY (cassation_comission_id) REFERENCES lucca_minute_commission (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_decision_linked_penalty ADD CONSTRAINT FK_1607E7BBBDEE7539 FOREIGN KEY (decision_id) REFERENCES lucca_minute_decision (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_decision_linked_penalty ADD CONSTRAINT FK_1607E7BB17C4A6C7 FOREIGN KEY (penalty_id) REFERENCES lucca_minute_penalty (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_decision_linked_liquidation ADD CONSTRAINT FK_231DFAEBBDEE7539 FOREIGN KEY (decision_id) REFERENCES lucca_minute_decision (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_decision_linked_liquidation ADD CONSTRAINT FK_231DFAEB90140D4C FOREIGN KEY (liquidation_id) REFERENCES lucca_minute_liquidation (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_decision_linked_penalty_appeal ADD CONSTRAINT FK_812B40E1BDEE7539 FOREIGN KEY (decision_id) REFERENCES lucca_minute_decision (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_decision_linked_penalty_appeal ADD CONSTRAINT FK_812B40E1311274DA FOREIGN KEY (penalty_appeal_id) REFERENCES lucca_minute_penalty_appeal (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_decision_linked_contradictory ADD CONSTRAINT FK_C805B7EFBDEE7539 FOREIGN KEY (decision_id) REFERENCES lucca_minute_decision (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_decision_linked_contradictory ADD CONSTRAINT FK_C805B7EF15AC53F7 FOREIGN KEY (contradictory_id) REFERENCES lucca_minute_contradictory (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_demolition ADD CONSTRAINT FK_EB2066FBDEE7539 FOREIGN KEY (decision_id) REFERENCES lucca_minute_decision (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_demolition_linked_profession ADD CONSTRAINT FK_90442EFFD1D77694 FOREIGN KEY (demolition_id) REFERENCES lucca_minute_demolition (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_demolition_linked_profession ADD CONSTRAINT FK_90442EFFFDEF8996 FOREIGN KEY (profession_id) REFERENCES lucca_minute_profession (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_expulsion ADD CONSTRAINT FK_3862D1C2BDEE7539 FOREIGN KEY (decision_id) REFERENCES lucca_minute_decision (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder ADD CONSTRAINT FK_952C70699D4D3F1B FOREIGN KEY (minute_id) REFERENCES lucca_minute (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder ADD CONSTRAINT FK_952C706932BEC70E FOREIGN KEY (control_id) REFERENCES lucca_minute_control (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder ADD CONSTRAINT FK_952C7069AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder ADD CONSTRAINT FK_952C7069E3D8151C FOREIGN KEY (courier_id) REFERENCES lucca_minute_courier (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder ADD CONSTRAINT FK_952C706974281A5E FOREIGN KEY (edition_id) REFERENCES lucca_minute_folder_edition (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder ADD CONSTRAINT FK_952C7069A0EC76E1 FOREIGN KEY (folder_signed_id) REFERENCES lucca_media (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder_linked_natinf ADD CONSTRAINT FK_7AA730A8162CB942 FOREIGN KEY (folder_id) REFERENCES lucca_minute_folder (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder_linked_natinf ADD CONSTRAINT FK_7AA730A8F87A39C6 FOREIGN KEY (natinf_id) REFERENCES lucca_natinf (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder_linked_tag_nature ADD CONSTRAINT FK_F1FBC128162CB942 FOREIGN KEY (folder_id) REFERENCES lucca_minute_folder (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder_linked_tag_nature ADD CONSTRAINT FK_F1FBC128BAD26311 FOREIGN KEY (tag_id) REFERENCES lucca_tag (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder_linked_tag_town ADD CONSTRAINT FK_BD8751FF162CB942 FOREIGN KEY (folder_id) REFERENCES lucca_minute_folder (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder_linked_tag_town ADD CONSTRAINT FK_BD8751FFBAD26311 FOREIGN KEY (tag_id) REFERENCES lucca_tag (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder_linked_human_minute ADD CONSTRAINT FK_1FFCB698162CB942 FOREIGN KEY (folder_id) REFERENCES lucca_minute_folder (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder_linked_human_minute ADD CONSTRAINT FK_1FFCB6988ABD4580 FOREIGN KEY (human_id) REFERENCES lucca_minute_human (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder_linked_human_folder ADD CONSTRAINT FK_9D9C14E0162CB942 FOREIGN KEY (folder_id) REFERENCES lucca_minute_folder (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder_linked_human_folder ADD CONSTRAINT FK_9D9C14E08ABD4580 FOREIGN KEY (human_id) REFERENCES lucca_minute_human (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_folder_linked_media ADD CONSTRAINT FK_3CDFC700C4663E4 FOREIGN KEY (page_id) REFERENCES lucca_minute_folder (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_folder_linked_media ADD CONSTRAINT FK_3CDFC700EA9FDD75 FOREIGN KEY (media_id) REFERENCES lucca_media (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder_element ADD CONSTRAINT FK_7F4CB9D2162CB942 FOREIGN KEY (folder_id) REFERENCES lucca_minute_folder (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder_element ADD CONSTRAINT FK_7F4CB9D23DA5256D FOREIGN KEY (image_id) REFERENCES lucca_media (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_human ADD CONSTRAINT FK_F7A3D5FCAE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_mayor_letter ADD CONSTRAINT FK_F13501DDAE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_mayor_letter ADD CONSTRAINT FK_F13501DD75E23604 FOREIGN KEY (town_id) REFERENCES lucca_parameter_town (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_mayor_letter ADD CONSTRAINT FK_F13501DD25F06C53 FOREIGN KEY (adherent_id) REFERENCES lucca_adherent (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_mayor_letter ADD CONSTRAINT FK_F13501DD3414710B FOREIGN KEY (agent_id) REFERENCES lucca_adherent_agent (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_mayor_letter_linked_folder ADD CONSTRAINT FK_F7480D3C52EB023D FOREIGN KEY (mayor_letter_id) REFERENCES lucca_minute_mayor_letter (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_mayor_letter_linked_folder ADD CONSTRAINT FK_F7480D3C162CB942 FOREIGN KEY (folder_id) REFERENCES lucca_minute_folder (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_minute_story ADD CONSTRAINT FK_927A47D39D4D3F1B FOREIGN KEY (minute_id) REFERENCES lucca_minute (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_minute_story ADD CONSTRAINT FK_927A47D3AA827920 FOREIGN KEY (updating_by_id) REFERENCES lucca_adherent (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_minute_story ADD CONSTRAINT FK_927A47D3AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_plot ADD CONSTRAINT FK_1759B03A75E23604 FOREIGN KEY (town_id) REFERENCES lucca_parameter_town (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_plot ADD CONSTRAINT FK_1759B03AAE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_profession ADD CONSTRAINT FK_AF63ACB9AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_updating ADD CONSTRAINT FK_328C3F4A9D4D3F1B FOREIGN KEY (minute_id) REFERENCES lucca_minute (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_updating ADD CONSTRAINT FK_328C3F4AAE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_updating_linked_control ADD CONSTRAINT FK_C89E8B0D215A0A56 FOREIGN KEY (updating_id) REFERENCES lucca_minute_updating (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_updating_linked_control ADD CONSTRAINT FK_C89E8B0D32BEC70E FOREIGN KEY (control_id) REFERENCES lucca_minute_control (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model ADD CONSTRAINT FK_DB0D99412AAEEA4A FOREIGN KEY (recto_id) REFERENCES lucca_model_page (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model ADD CONSTRAINT FK_DB0D9941AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model ADD CONSTRAINT FK_DB0D9941D44EE99E FOREIGN KEY (verso_id) REFERENCES lucca_model_page (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model ADD CONSTRAINT FK_DB0D99417E3C61F9 FOREIGN KEY (owner_id) REFERENCES lucca_adherent (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model ADD CONSTRAINT FK_DB0D9941994F4929 FOREIGN KEY (shared_service_id) REFERENCES lucca_parameter_service (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model ADD CONSTRAINT FK_DB0D994199886B3E FOREIGN KEY (shared_intercommunal_id) REFERENCES lucca_parameter_intercommunal (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model ADD CONSTRAINT FK_DB0D994154353AB3 FOREIGN KEY (shared_town_id) REFERENCES lucca_parameter_town (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model_bloc ADD CONSTRAINT FK_5592B511EFB9A5B6 FOREIGN KEY (margin_id) REFERENCES lucca_model_margin (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model_bloc ADD CONSTRAINT FK_5592B511AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model_bloc ADD CONSTRAINT FK_5592B511EA9FDD75 FOREIGN KEY (media_id) REFERENCES lucca_media (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model_bloc ADD CONSTRAINT FK_5592B51165310DAC FOREIGN KEY (background_img_id) REFERENCES lucca_media (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model_margin ADD CONSTRAINT FK_1B82EE35AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model_margin ADD CONSTRAINT FK_1B82EE3565310DAC FOREIGN KEY (background_img_id) REFERENCES lucca_media (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model_page ADD CONSTRAINT FK_86E0966BAE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model_page ADD CONSTRAINT FK_86E0966BF9FB2723 FOREIGN KEY (margin_top_id) REFERENCES lucca_model_margin (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model_page ADD CONSTRAINT FK_86E0966B1856F4B6 FOREIGN KEY (margin_bottom_id) REFERENCES lucca_model_margin (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model_page ADD CONSTRAINT FK_86E0966BC2329C24 FOREIGN KEY (margin_left_id) REFERENCES lucca_model_margin (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model_page ADD CONSTRAINT FK_86E0966B86BAB39A FOREIGN KEY (margin_right_id) REFERENCES lucca_model_margin (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_natinf ADD CONSTRAINT FK_254257C9AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_natinf ADD CONSTRAINT FK_254257C9727ACA70 FOREIGN KEY (parent_id) REFERENCES lucca_natinf (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_natinf_linked_tag ADD CONSTRAINT FK_6CF917EFF87A39C6 FOREIGN KEY (natinf_id) REFERENCES lucca_natinf (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_natinf_linked_tag ADD CONSTRAINT FK_6CF917EFBAD26311 FOREIGN KEY (tag_id) REFERENCES lucca_tag (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_parameter_intercommunal ADD CONSTRAINT FK_2B223A6BAE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_parameter_intercommunal ADD CONSTRAINT FK_2B223A6BFFA0C224 FOREIGN KEY (office_id) REFERENCES lucca_parameter_town (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_parameter_partner ADD CONSTRAINT FK_A1FA699EAE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_parameter_service ADD CONSTRAINT FK_714CCD5AAE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_parameter_service ADD CONSTRAINT FK_714CCD5AFFA0C224 FOREIGN KEY (office_id) REFERENCES lucca_parameter_town (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_parameter_town ADD CONSTRAINT FK_E7FAE590AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_parameter_town ADD CONSTRAINT FK_E7FAE59040B6387D FOREIGN KEY (intercommunal_id) REFERENCES lucca_parameter_intercommunal (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_parameter_tribunal ADD CONSTRAINT FK_3F7FE0EAAE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_parameter_tribunal ADD CONSTRAINT FK_3F7FE0EAFFA0C224 FOREIGN KEY (office_id) REFERENCES lucca_parameter_town (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_proposal ADD CONSTRAINT FK_FBB4C67BAD26311 FOREIGN KEY (tag_id) REFERENCES lucca_tag (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_proposal ADD CONSTRAINT FK_FBB4C67AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_setting ADD CONSTRAINT FK_11348FE212469DE2 FOREIGN KEY (category_id) REFERENCES lucca_setting_category (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_setting ADD CONSTRAINT FK_11348FE2AE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_tag ADD CONSTRAINT FK_4724FDFEAE80F5DF FOREIGN KEY (department_id) REFERENCES lucca_department (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_user_linked_group ADD CONSTRAINT FK_7873C38A76ED395 FOREIGN KEY (user_id) REFERENCES lucca_user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_user_linked_group ADD CONSTRAINT FK_7873C38FE54D947 FOREIGN KEY (group_id) REFERENCES lucca_user_group (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent DROP FOREIGN KEY FK_208D2875A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent DROP FOREIGN KEY FK_208D287575E23604
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent DROP FOREIGN KEY FK_208D287540B6387D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent DROP FOREIGN KEY FK_208D2875ED5CA9E6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent DROP FOREIGN KEY FK_208D2875F98F144A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent_linked_department DROP FOREIGN KEY FK_AE2F95D525F06C53
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent_linked_department DROP FOREIGN KEY FK_AE2F95D5AE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent_agent DROP FOREIGN KEY FK_2193A0B0AE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent_agent DROP FOREIGN KEY FK_2193A0B075C2CEC3
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_adherent_agent DROP FOREIGN KEY FK_2193A0B025F06C53
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_checklist DROP FOREIGN KEY FK_3104D71CAE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_checklist_element DROP FOREIGN KEY FK_77A875CBB16D08A7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_content_area DROP FOREIGN KEY FK_11F2D0DEAE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_content_page DROP FOREIGN KEY FK_D26C5B96F66DE853
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_content_page DROP FOREIGN KEY FK_D26C5B96F675F31B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_content_page DROP FOREIGN KEY FK_D26C5B96AE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_content_page_linked_media DROP FOREIGN KEY FK_7838D5CBC4663E4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_content_page_linked_media DROP FOREIGN KEY FK_7838D5CBEA9FDD75
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_content_subarea DROP FOREIGN KEY FK_80F3844BBD0F409C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_content_subarea DROP FOREIGN KEY FK_80F3844BAE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_log DROP FOREIGN KEY FK_CB9222B8A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_media DROP FOREIGN KEY FK_66B44A9412469DE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_media DROP FOREIGN KEY FK_66B44A94162CB942
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_media DROP FOREIGN KEY FK_66B44A947E3C61F9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_media_category DROP FOREIGN KEY FK_D3F1642AF9E8CDF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_media_category_linked_meta_data_model DROP FOREIGN KEY FK_37E2319412469DE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_media_category_linked_meta_data_model DROP FOREIGN KEY FK_37E23194C9E25533
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_media_category_linked_extension DROP FOREIGN KEY FK_336D379612469DE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_media_category_linked_extension DROP FOREIGN KEY FK_336D3796812D5EB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_media_gallery DROP FOREIGN KEY FK_EFFBDAF5ECDBED3B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_media_gallery_linked_media DROP FOREIGN KEY FK_5D42AD8B4E7AF8F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_media_gallery_linked_media DROP FOREIGN KEY FK_5D42AD8BEA9FDD75
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_media_meta_data DROP FOREIGN KEY FK_4AEF21B5EA9FDD75
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_media_storager_linked_folder DROP FOREIGN KEY FK_B0394800AF9E8CDF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_media_storager_linked_folder DROP FOREIGN KEY FK_B0394800162CB942
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute DROP FOREIGN KEY FK_901A282825F06C53
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute DROP FOREIGN KEY FK_901A2828680D0B01
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute DROP FOREIGN KEY FK_901A2828AE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute DROP FOREIGN KEY FK_901A282875C2CEC3
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute DROP FOREIGN KEY FK_901A28281832BF33
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute DROP FOREIGN KEY FK_901A28283414710B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute DROP FOREIGN KEY FK_901A2828FA43E9DA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_linked_human DROP FOREIGN KEY FK_358F79939D4D3F1B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_linked_human DROP FOREIGN KEY FK_358F79938ABD4580
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_closure DROP FOREIGN KEY FK_99C564599D4D3F1B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_closure DROP FOREIGN KEY FK_99C56459AE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_control DROP FOREIGN KEY FK_3C19C5C39D4D3F1B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_control DROP FOREIGN KEY FK_3C19C5C33414710B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_control DROP FOREIGN KEY FK_3C19C5C3AE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_control DROP FOREIGN KEY FK_3C19C5C3162CB942
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_control_linked_human_minute DROP FOREIGN KEY FK_CA3FE67532BEC70E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_control_linked_human_minute DROP FOREIGN KEY FK_CA3FE6758ABD4580
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_control_linked_human_control DROP FOREIGN KEY FK_761B13B632BEC70E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_control_linked_human_control DROP FOREIGN KEY FK_761B13B68ABD4580
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_control_linked_agent_attendant DROP FOREIGN KEY FK_3044996132BEC70E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_control_linked_agent_attendant DROP FOREIGN KEY FK_304499613BAC4DFE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_control_edition DROP FOREIGN KEY FK_F6DD0F4E32BEC70E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_control_edition DROP FOREIGN KEY FK_F6DD0F4E8ABD4580
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_courier DROP FOREIGN KEY FK_1ED1A5F4162CB942
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_courier DROP FOREIGN KEY FK_1ED1A5F4AE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_courier DROP FOREIGN KEY FK_1ED1A5F474281A5E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_courier_human_edition DROP FOREIGN KEY FK_8510A934E3D8151C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_courier_human_edition DROP FOREIGN KEY FK_8510A9348ABD4580
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_decision DROP FOREIGN KEY FK_671E77B39D4D3F1B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_decision DROP FOREIGN KEY FK_671E77B375C2CEC3
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_decision DROP FOREIGN KEY FK_671E77B3CA50AE44
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_decision DROP FOREIGN KEY FK_671E77B3AE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_decision DROP FOREIGN KEY FK_671E77B3BD7F0960
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_decision DROP FOREIGN KEY FK_671E77B3D193C759
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_decision_linked_penalty DROP FOREIGN KEY FK_1607E7BBBDEE7539
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_decision_linked_penalty DROP FOREIGN KEY FK_1607E7BB17C4A6C7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_decision_linked_liquidation DROP FOREIGN KEY FK_231DFAEBBDEE7539
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_decision_linked_liquidation DROP FOREIGN KEY FK_231DFAEB90140D4C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_decision_linked_penalty_appeal DROP FOREIGN KEY FK_812B40E1BDEE7539
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_decision_linked_penalty_appeal DROP FOREIGN KEY FK_812B40E1311274DA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_decision_linked_contradictory DROP FOREIGN KEY FK_C805B7EFBDEE7539
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_decision_linked_contradictory DROP FOREIGN KEY FK_C805B7EF15AC53F7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_demolition DROP FOREIGN KEY FK_EB2066FBDEE7539
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_demolition_linked_profession DROP FOREIGN KEY FK_90442EFFD1D77694
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_demolition_linked_profession DROP FOREIGN KEY FK_90442EFFFDEF8996
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_expulsion DROP FOREIGN KEY FK_3862D1C2BDEE7539
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder DROP FOREIGN KEY FK_952C70699D4D3F1B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder DROP FOREIGN KEY FK_952C706932BEC70E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder DROP FOREIGN KEY FK_952C7069AE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder DROP FOREIGN KEY FK_952C7069E3D8151C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder DROP FOREIGN KEY FK_952C706974281A5E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder DROP FOREIGN KEY FK_952C7069A0EC76E1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder_linked_natinf DROP FOREIGN KEY FK_7AA730A8162CB942
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder_linked_natinf DROP FOREIGN KEY FK_7AA730A8F87A39C6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder_linked_tag_nature DROP FOREIGN KEY FK_F1FBC128162CB942
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder_linked_tag_nature DROP FOREIGN KEY FK_F1FBC128BAD26311
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder_linked_tag_town DROP FOREIGN KEY FK_BD8751FF162CB942
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder_linked_tag_town DROP FOREIGN KEY FK_BD8751FFBAD26311
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder_linked_human_minute DROP FOREIGN KEY FK_1FFCB698162CB942
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder_linked_human_minute DROP FOREIGN KEY FK_1FFCB6988ABD4580
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder_linked_human_folder DROP FOREIGN KEY FK_9D9C14E0162CB942
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder_linked_human_folder DROP FOREIGN KEY FK_9D9C14E08ABD4580
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_folder_linked_media DROP FOREIGN KEY FK_3CDFC700C4663E4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_folder_linked_media DROP FOREIGN KEY FK_3CDFC700EA9FDD75
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder_element DROP FOREIGN KEY FK_7F4CB9D2162CB942
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_folder_element DROP FOREIGN KEY FK_7F4CB9D23DA5256D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_human DROP FOREIGN KEY FK_F7A3D5FCAE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_mayor_letter DROP FOREIGN KEY FK_F13501DDAE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_mayor_letter DROP FOREIGN KEY FK_F13501DD75E23604
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_mayor_letter DROP FOREIGN KEY FK_F13501DD25F06C53
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_mayor_letter DROP FOREIGN KEY FK_F13501DD3414710B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_mayor_letter_linked_folder DROP FOREIGN KEY FK_F7480D3C52EB023D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_mayor_letter_linked_folder DROP FOREIGN KEY FK_F7480D3C162CB942
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_minute_story DROP FOREIGN KEY FK_927A47D39D4D3F1B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_minute_story DROP FOREIGN KEY FK_927A47D3AA827920
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_minute_story DROP FOREIGN KEY FK_927A47D3AE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_plot DROP FOREIGN KEY FK_1759B03A75E23604
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_plot DROP FOREIGN KEY FK_1759B03AAE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_profession DROP FOREIGN KEY FK_AF63ACB9AE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_updating DROP FOREIGN KEY FK_328C3F4A9D4D3F1B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_updating DROP FOREIGN KEY FK_328C3F4AAE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_updating_linked_control DROP FOREIGN KEY FK_C89E8B0D215A0A56
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_minute_updating_linked_control DROP FOREIGN KEY FK_C89E8B0D32BEC70E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model DROP FOREIGN KEY FK_DB0D99412AAEEA4A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model DROP FOREIGN KEY FK_DB0D9941AE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model DROP FOREIGN KEY FK_DB0D9941D44EE99E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model DROP FOREIGN KEY FK_DB0D99417E3C61F9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model DROP FOREIGN KEY FK_DB0D9941994F4929
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model DROP FOREIGN KEY FK_DB0D994199886B3E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model DROP FOREIGN KEY FK_DB0D994154353AB3
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model_bloc DROP FOREIGN KEY FK_5592B511EFB9A5B6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model_bloc DROP FOREIGN KEY FK_5592B511AE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model_bloc DROP FOREIGN KEY FK_5592B511EA9FDD75
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model_bloc DROP FOREIGN KEY FK_5592B51165310DAC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model_margin DROP FOREIGN KEY FK_1B82EE35AE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model_margin DROP FOREIGN KEY FK_1B82EE3565310DAC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model_page DROP FOREIGN KEY FK_86E0966BAE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model_page DROP FOREIGN KEY FK_86E0966BF9FB2723
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model_page DROP FOREIGN KEY FK_86E0966B1856F4B6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model_page DROP FOREIGN KEY FK_86E0966BC2329C24
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_model_page DROP FOREIGN KEY FK_86E0966B86BAB39A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_natinf DROP FOREIGN KEY FK_254257C9AE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_natinf DROP FOREIGN KEY FK_254257C9727ACA70
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_natinf_linked_tag DROP FOREIGN KEY FK_6CF917EFF87A39C6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_natinf_linked_tag DROP FOREIGN KEY FK_6CF917EFBAD26311
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_parameter_intercommunal DROP FOREIGN KEY FK_2B223A6BAE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_parameter_intercommunal DROP FOREIGN KEY FK_2B223A6BFFA0C224
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_parameter_partner DROP FOREIGN KEY FK_A1FA699EAE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_parameter_service DROP FOREIGN KEY FK_714CCD5AAE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_parameter_service DROP FOREIGN KEY FK_714CCD5AFFA0C224
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_parameter_town DROP FOREIGN KEY FK_E7FAE590AE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_parameter_town DROP FOREIGN KEY FK_E7FAE59040B6387D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_parameter_tribunal DROP FOREIGN KEY FK_3F7FE0EAAE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_parameter_tribunal DROP FOREIGN KEY FK_3F7FE0EAFFA0C224
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_proposal DROP FOREIGN KEY FK_FBB4C67BAD26311
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_proposal DROP FOREIGN KEY FK_FBB4C67AE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_setting DROP FOREIGN KEY FK_11348FE212469DE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_setting DROP FOREIGN KEY FK_11348FE2AE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_tag DROP FOREIGN KEY FK_4724FDFEAE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_user_linked_group DROP FOREIGN KEY FK_7873C38A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lucca_user_linked_group DROP FOREIGN KEY FK_7873C38FE54D947
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_adherent
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_adherent_linked_department
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_adherent_agent
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_checklist
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_checklist_element
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_content_area
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_content_page
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_content_page_linked_media
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_content_subarea
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_department
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_log
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_media
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_media_category
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_media_category_linked_meta_data_model
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_media_category_linked_extension
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_media_extension
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_media_folder
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_media_gallery
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_media_gallery_linked_media
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_media_meta_data
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_media_meta_data_model
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_media_storager
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_media_storager_linked_folder
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_linked_human
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_agent_attendant
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_closure
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_commission
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_contradictory
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_control
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_control_linked_human_minute
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_control_linked_human_control
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_control_linked_agent_attendant
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_control_edition
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_courier
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_courier_edition
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_courier_human_edition
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_decision
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_decision_linked_penalty
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_decision_linked_liquidation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_decision_linked_penalty_appeal
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_decision_linked_contradictory
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_demolition
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_demolition_linked_profession
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_expulsion
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_folder
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_folder_linked_natinf
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_folder_linked_tag_nature
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_folder_linked_tag_town
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_folder_linked_human_minute
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_folder_linked_human_folder
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_folder_linked_media
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_folder_edition
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_folder_element
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_human
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_liquidation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_mayor_letter
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_mayor_letter_linked_folder
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_minute_story
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_penalty
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_penalty_appeal
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_plot
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_profession
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_updating
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_minute_updating_linked_control
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_model
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_model_bloc
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_model_margin
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_model_page
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_natinf
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_natinf_linked_tag
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_parameter_intercommunal
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_parameter_partner
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_parameter_service
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_parameter_town
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_parameter_tribunal
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_proposal
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_security_login_attempt
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_setting
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_setting_category
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_tag
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_user_linked_group
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lucca_user_group
        SQL);
    }
}
