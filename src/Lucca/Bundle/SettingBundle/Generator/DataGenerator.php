<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SettingBundle\Generator;

use Lucca\Bundle\SettingBundle\Entity\Setting;

class DataGenerator
{
    /** Don't forget to add the translations */

    // Define all the categories needed /!\ don't forgot any category
    public array $categories = [
        [
            'name' => "setting.category.general.name",
            'icon' => "fas fa-wrench",
            'position' => 0,
            'comment' => "setting.category.general.comment"],
        [
            'name' => "setting.category.map.name",
            'icon' => "fas fa-map",
            'position' => 1,
            'comment' => "setting.category.map.comment"],
        [
            'name' => "setting.category.pdf.name",
            'icon' => "fas fa-file-pdf",
            'position' => 2,
            'comment' => "setting.category.pdf.comment"],
        [
            'name' => "setting.category.folder.name",
            'icon' => "fas fa-folder",
            'position' => 3,
            'comment' => "setting.category.folder.comment"],
        [
            'name' => "setting.category.courier.name",
            'icon' => "fas fa-envelope",
            'position' => 4,
            'comment' => "setting.category.courier.comment"],
        [
            'name' => "setting.category.control.name",
            'icon' => "fas fa-clipboard-check",
            'position' => 5,
            'comment' => "setting.category.control.comment"],
        [
            'name' => "setting.category.module.name",
            'icon' => "fas fa-chart-network",
            'position' => 6,
            'comment' => "setting.category.module.comment"],
    ];

    // Define all the settings needed
    // setting.categoryName.parameterName.name
    // categoryName -> name of the category in the previous array
    // parameterName -> name of the parameter
    public array $settings = [
        //------------------------------
        // GENERAL SETTINGS
        //------------------------------
        [
            'name' => "setting.general.app.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_ADMIN,
            'position' => 0,
            'value' => 'Lucca',
            'valuesAvailable' => [],
            'comment' => 'setting.general.app.comment'],
        [
            'name' => "setting.general.bannerTop.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 0,
            'value' => '',
            'valuesAvailable' => [],
            'comment' => 'setting.general.bannerTop.comment'],
        [
            'name' => "setting.general.departement.name",
            'type' => Setting::TYPE_LIST,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 1,
            'value' => 'A renseigner dans les paramètres',
            'valuesAvailable' => [
                '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17',
                '18', '19', '2A', '2B', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33',
                '34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46', '47', '48', '49', '50',
                '51', '52', '53', '54', '55', '56', '57', '58', '59', '60', '61', '62', '63', '64', '65', '66', '67',
                '68', '69', '70', '71', '72', '73', '74', '75', '76', '77', '78', '79', '80', '81', '82', '83', '84',
                '85', '86', '87', '88', '89', '90', '91', '92', '93', '94', '95', '971', '972', '973', '974', '976'
            ],
            'comment' => 'setting.general.departement.comment'],
        [
            'name' => "setting.general.colorL.name",
            'type' => Setting::TYPE_COLOR,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_ADMIN,
            'position' => 2,
            'value' => '#23b7e5',
            'valuesAvailable' => [],
            'comment' => 'setting.general.colorL.comment'],
        [
            'name' => "setting.general.colorR.name",
            'type' => Setting::TYPE_COLOR,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_ADMIN,
            'position' => 3,
            'value' => '#51c6ea',
            'valuesAvailable' => [],
            'comment' => 'setting.general.colorR.comment'],
        [
            'name' => "setting.general.emailGlobal.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_ADMIN,
            'position' => 4,
            'value' => 'A renseigner dans les paramètres',
            'valuesAvailable' => [],
            'comment' => 'setting.general.emailGlobal.comment'],
        [
            'name' => "setting.general.emailLegaleDepartement.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_ADMIN,
            'position' => 5,
            'value' => 'A renseigner dans les paramètres',
            'valuesAvailable' => [],
            'comment' => 'setting.general.emailLegaleDepartement.comment'],
        [
            'name' => "setting.general.logo.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 6,
            'value' => 'assets/logo/lucca-logo-texte-transparent.png',
            'valuesAvailable' => [],
            'comment' => 'setting.general.logo.comment'],
        [
            'name' => "setting.general.ddtName.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_ADMIN,
            'position' => 7,
            'value' => 'A renseigner dans les paramètres',
            'valuesAvailable' => [],
            'comment' => 'setting.general.ddtName.comment'],
        //------------------------------
        // CARTO SETTINGS
        //------------------------------
        [
            'name' => "setting.map.mapActive.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.map.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 8,
            'value' => true,
            'valuesAvailable' => [],
            'comment' => 'setting.map.mapActive.comment'],
        [
            'name' => "setting.map.mapKey.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.map.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 9,
            'value' => '',
            'valuesAvailable' => [],
            'comment' => 'setting.map.mapKey.comment'],
        [
            'name' => "setting.map.geocodeKey.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.map.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 10,
            'value' => '',
            'valuesAvailable' => [],
            'comment' => 'setting.map.geocodeKey.comment'],
        [
            'name' => "setting.map.lat.name",
            'type' => Setting::TYPE_FLOAT,
            'category' => 'setting.category.map.name',
            'accessType' => Setting::ACCESS_TYPE_ADMIN,
            'position' => 11,
            'value' => '43.60347',
            'valuesAvailable' => [],
            'comment' => 'setting.map.lat.comment'],
        [
            'name' => "setting.map.lon.name",
            'type' => Setting::TYPE_FLOAT,
            'category' => 'setting.category.map.name',
            'accessType' => Setting::ACCESS_TYPE_ADMIN,
            'position' => 12,
            'value' => '3.8763421',
            'valuesAvailable' => [],
            'comment' => 'setting.map.lon.comment'],
        //------------------------------
        // PDF SETTINGS
        //------------------------------
        [
            'name' => "setting.pdf.logo.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.pdf.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 13,
            'value' => '/assets/logo/logo-color.png',
            'valuesAvailable' => [],
            'comment' => 'setting.pdf.logo.comment'],
        [
            'name' => "setting.pdf.logoInHeader.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.pdf.name',
            'accessType' => Setting::ACCESS_TYPE_ADMIN,
            'position' => 14,
            'value' => false,
            'valuesAvailable' => [],
            'comment' => 'setting.pdf.logoInHeader.comment'],
        //------------------------------
        // FOLDER SETTINGS
        //------------------------------
        [
            'name' => "setting.folder.docFooterDueDate.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.folder.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 15,
            'value' => true,
            'valuesAvailable' => [],
            'comment' => 'setting.folder.docFooterDueDate.comment'],
        [
            'name' => "setting.folder.docContent.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.folder.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 16,
            'value' => false,
            'valuesAvailable' => [],
            'comment' => 'setting.folder.docContent.comment'],
        [
            'name' => "setting.folder.docContentObstacle.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.folder.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 17,
            'value' => false,
            'valuesAvailable' => [],
            'comment' => 'setting.folder.docContentObstacle.comment'],
        [
            'name' => "setting.folder.blockAttachmentToChecklist.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.folder.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 18,
            'value' => false,
            'valuesAvailable' => [],
            'comment' => 'setting.folder.blockAttachmentToChecklist.comment'],
        [
            'name' => "setting.folder.useRefreshAgentForRefreshSignature.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.folder.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 19,
            'value' => false,
            'valuesAvailable' => [],
            'comment' => 'setting.folder.useRefreshAgentForRefreshSignature.comment'],
        [
            'name' => "setting.folder.indexFilterByRollingOrCalendarYear.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.folder.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 20,
            'value' => false,
            'valuesAvailable' => [],
            'comment' => 'setting.folder.indexFilterByRollingOrCalendarYear.comment'],
        [
            'name' => "setting.folder.presetFilterAdherentByConnectedUser.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.folder.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 21,
            'value' => false,
            'valuesAvailable' => [],
            'comment' => 'setting.folder.presetFilterAdherentByConnectedUser.comment'],

        //------------------------------
        // COURIER SETTINGS
        //------------------------------
        [
            'name' => "setting.courier.offenderContent.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.courier.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 18,
            'value' => false,
            'valuesAvailable' => [],
            'comment' => 'setting.courier.offenderContent.comment'],
        [
            'name' => "setting.courier.ddtmContent.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.courier.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 19,
            'value' => false,
            'valuesAvailable' => [],
            'comment' => 'setting.courier.ddtmContent.comment'],
        [
            'name' => "setting.courier.judicialContent.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.courier.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 20,
            'value' => false,
            'valuesAvailable' => [],
            'comment' => 'setting.courier.judicialContent.comment'],
        [
            'name' => "setting.courier.mayorContent.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.courier.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 21,
            'value' => false,
            'valuesAvailable' => [],
            'comment' => 'setting.courier.mayorContent.comment'],
        //------------------------------
        // CONTROL SETTINGS
        //------------------------------
        [
            'name' => "setting.control.footer.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.control.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 21,
            'value' => false,
            'valuesAvailable' => [],
            'comment' => 'setting.control.footer.comment'],
        [
            'name' => "setting.control.accessContent.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.control.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 22,
            'value' => false,
            'valuesAvailable' => [],
            'comment' => 'setting.control.accessContent.comment'],
        [
            'name' => "setting.control.accessEmpty.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.control.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 23,
            'value' => false,
            'valuesAvailable' => [],
            'comment' => 'setting.control.accessEmpty.comment'],
        [
            'name' => "setting.control.convocationContent.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.control.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 24,
            'value' => false,
            'valuesAvailable' => [],
            'comment' => 'setting.control.convocationContent.comment'],
        //------------------------------
        // GENERAL - URL SETTINGS
        //------------------------------
        [
            'name' => "setting.general.url.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_ADMIN,
            'position' => 26,
            'value' => "A renseigner dans les paramètres",
            'valuesAvailable' => [],
            'comment' => 'setting.general.url.comment'],
        [
            'name' => "setting.general.urlGouv.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_ADMIN,
            'position' => 27,
            'value' => "A renseigner dans les paramètres",
            'valuesAvailable' => [],
            'comment' => 'setting.general.urlGouv.comment'],
        [
            'name' => "setting.general.prefixUsername.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 28,
            'value' => "lucca",
            'valuesAvailable' => [],
            'comment' => 'setting.general.prefixUsername.comment'],
        [
            'name' => "setting.map.maxResults.name",
            'type' => Setting::TYPE_INTEGER,
            'category' => 'setting.category.map.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 29,
            'value' => 100,
            'valuesAvailable' => [],
            'comment' => 'setting.map.maxResults.comment'],
        [
            'name' => "setting.general.ddtAcronym.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_ADMIN,
            'position' => 30,
            'value' => 'DDTM',
            'valuesAvailable' => ['DDT', 'DDTM'],
            'comment' => 'setting.general.ddtAcronym.comment'],
        [
            'name' => "setting.general.catchphrase1.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_ADMIN,
            'position' => 31,
            'value' => 'Lutte contre la cabanisation',
            'valuesAvailable' => [],
            'comment' => 'setting.general.catchphrase1.comment'],
        [
            'name' => "setting.general.catchphrase2.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_ADMIN,
            'position' => 32,
            'value' => 'et autres infractions à l\'urbanisme',
            'valuesAvailable' => [],
            'comment' => 'setting.general.catchphrase2.comment'],

        //------------------------------
        // MODULE ACCESS SETTINGS
        //------------------------------
        [
            'name' => "setting.module.mayorletter.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.module.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 33,
            'value' => false,
            'valuesAvailable' => [],
            'comment' => 'setting.module.mayorletter.comment'],
        [
            'name' => "setting.module.dashboardAdmin.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.module.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 13,
            'value' => true,
            'valuesAvailable' => [],
            'comment' => 'setting.module.dashboardAdmin.comment'],

        //------------------------------
        // General URL converter PDF into JPEG
        //------------------------------
        [
            'name' => "setting.general.converterLink.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_ADMIN,
            'position' => 1,
            'value' => 'https://pdftoimage.com/fr/',
            'valuesAvailable' => [],
            'comment' => 'setting.general.converterLink.comment'],

        //------------------------------
        // Annexes in folder
        //------------------------------
        [
            'name' => "setting.module.annexes.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.module.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 1,
            'value' => false,
            'valuesAvailable' => [],
            'comment' => 'setting.module.annexes.comment'],
        [
            'name' => "setting.folder.annexesQuantity.name",
            'type' => Setting::TYPE_INTEGER,
            'category' => 'setting.category.folder.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 2,
            'value' => 3,
            'valuesAvailable' => [],
            'comment' => 'setting.folder.annexesQuantity.comment'],
        [
            'name' => "setting.folder.annexesMaxSize.name",
            'type' => Setting::TYPE_INTEGER,
            'category' => 'setting.category.folder.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 2,
            'value' => 25,
            'valuesAvailable' => [],
            'comment' => 'setting.folder.annexesMaxSize.comment'],
    ];
}
