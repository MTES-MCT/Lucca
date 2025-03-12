<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
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
        1 => [
            'name' => "setting.category.general.name",
            'icon' => "fas fa-wrench",
            'position' => 0,
            'comment' => "setting.category.general.comment"],
        2 => [
            'name' => "setting.category.map.name",
            'icon' => "fas fa-map",
            'position' => 1,
            'comment' => "setting.category.map.comment"],
        3 => [
            'name' => "setting.category.pdf.name",
            'icon' => "fas fa-file-pdf",
            'position' => 2,
            'comment' => "setting.category.pdf.comment"],
        4 => [
            'name' => "setting.category.folder.name",
            'icon' => "fas fa-folder",
            'position' => 3,
            'comment' => "setting.category.folder.comment"],
        5 => [
            'name' => "setting.category.courier.name",
            'icon' => "fas fa-envelope",
            'position' => 4,
            'comment' => "setting.category.courier.comment"],
        6 => [
            'name' => "setting.category.control.name",
            'icon' => "fas fa-clipboard-check",
            'position' => 5,
            'comment' => "setting.category.control.comment"],
        7 => [
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
        1 => [
            'name' => "setting.general.app.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_ADMIN,
            'position' => 0,
            'value' => 'Lucca',
            'valuesAvailable' => [],
            'comment' => 'setting.general.app.comment'],
        2 => [
            'name' => "setting.general.departement.name",
            'type' => Setting::TYPE_LIST,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 1,
            'value' => '66',
            'valuesAvailable' => ['66', '34', '12', '31', '33'],
            'comment' => 'setting.general.departement.comment'],
        3 => [
            'name' => "setting.general.colorL.name",
            'type' => Setting::TYPE_COLOR,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_ADMIN,
            'position' => 2,
            'value' => '#23b7e5',
            'valuesAvailable' => [],
            'comment' => 'setting.general.colorL.comment'],
        4 => [
            'name' => "setting.general.colorR.name",
            'type' => Setting::TYPE_COLOR,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_ADMIN,
            'position' => 3,
            'value' => '#51c6ea',
            'valuesAvailable' => [],
            'comment' => 'setting.general.colorR.comment'],
        5 => [
            'name' => "setting.general.emailGlobal.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_ADMIN,
            'position' => 4,
            'value' => 'subscription@cabanisation66.fr',
            'valuesAvailable' => [],
            'comment' => 'setting.general.emailGlobal.comment'],
        6 => [
            'name' => "setting.general.emailLegaleDepartement.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_ADMIN,
            'position' => 5,
            'value' => 'ddtm-cabanisation@pyrenees-orientales.gouv.fr',
            'valuesAvailable' => [],
            'comment' => 'setting.general.emailLegaleDepartement.comment'],
        7 => [
            'name' => "setting.general.logo.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 6,
            'value' => 'assets/logo/lucca-logo-texte-transparent.png',
            'valuesAvailable' => [],
            'comment' => 'setting.general.logo.comment'],
        8 => [
            'name' => "setting.general.ddtName.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_ADMIN,
            'position' => 7,
            'value' => 'DDT 66 des Pyrenees Orientales',
            'valuesAvailable' => [],
            'comment' => 'setting.general.ddtName.comment'],
        //------------------------------
        // CARTO SETTINGS
        //------------------------------
        10 => [
            'name' => "setting.map.mapActive.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.map.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 8,
            'value' => true,
            'valuesAvailable' => [],
            'comment' => 'setting.map.mapActive.comment'],
        11 => [
            'name' => "setting.map.mapKey.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.map.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 9,
            'value' => 'AIzaSyBCaacz1oZPfGnmfX71aNm9dEpPII4CWOA',
            'valuesAvailable' => [],
            'comment' => 'setting.map.mapKey.comment'],
        12 => [
            'name' => "setting.map.geocodeKey.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.map.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 10,
            'value' => 'AIzaSyAQKSy-LJ-9IGhgDSx3FbdpyT8ZDuUI-xg',
            'valuesAvailable' => [],
            'comment' => 'setting.map.geocodeKey.comment'],
        13 => [
            'name' => "setting.map.lat.name",
            'type' => Setting::TYPE_FLOAT,
            'category' => 'setting.category.map.name',
            'accessType' => Setting::ACCESS_TYPE_ADMIN,
            'position' => 11,
            'value' => '43.60347',
            'valuesAvailable' => [],
            'comment' => 'setting.map.lat.comment'],
        14 => [
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
        15 => [
            'name' => "setting.pdf.logo.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.pdf.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 13,
            'value' => '/assets/12/logo-marianne-pdf.jpg',
            'valuesAvailable' => [],
            'comment' => 'setting.pdf.logo.comment'],
        16 => [
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
        17 => [
            'name' => "setting.folder.docFooterDueDate.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.folder.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 15,
            'value' => true,
            'valuesAvailable' => [],
            'comment' => 'setting.folder.docFooterDueDate.comment'],
        18 => [
            'name' => "setting.folder.docContent.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.folder.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 16,
            'value' => true,
            'valuesAvailable' => [],
            'comment' => 'setting.folder.docContent.comment'],
        19 => [
            'name' => "setting.folder.docContentObstacle.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.folder.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 17,
            'value' => true,
            'valuesAvailable' => [],
            'comment' => 'setting.folder.docContentObstacle.comment'],
        20 => [
            'name' => "setting.folder.blockAttachmentToChecklist.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.folder.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 18,
            'value' => false,
            'valuesAvailable' => [],
            'comment' => 'setting.folder.blockAttachmentToChecklist.comment'],
        21 => [
            'name' => "setting.folder.useRefreshAgentForRefreshSignature.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.folder.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 19,
            'value' => false,
            'valuesAvailable' => [],
            'comment' => 'setting.folder.useRefreshAgentForRefreshSignature.comment'],
        22 => [
            'name' => "setting.folder.indexFilterByRollingOrCalendarYear.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.folder.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 20,
            'value' => false,
            'valuesAvailable' => [],
            'comment' => 'setting.folder.indexFilterByRollingOrCalendarYear.comment'],
        23 => [
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
        24 => [
            'name' => "setting.courier.offenderContent.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.courier.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 18,
            'value' => false,
            'valuesAvailable' => [],
            'comment' => 'setting.courier.offenderContent.comment'],
        25 => [
            'name' => "setting.courier.ddtmContent.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.courier.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 19,
            'value' => true,
            'valuesAvailable' => [],
            'comment' => 'setting.courier.ddtmContent.comment'],
        26 => [
            'name' => "setting.courier.judicialContent.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.courier.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 20,
            'value' => true,
            'valuesAvailable' => [],
            'comment' => 'setting.courier.judicialContent.comment'],
        27 => [
            'name' => "setting.courier.mayorContent.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.courier.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 21,
            'value' => true,
            'valuesAvailable' => [],
            'comment' => 'setting.courier.mayorContent.comment'],
        //------------------------------
        // CONTROL SETTINGS
        //------------------------------
        28 => [
            'name' => "setting.control.footer.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.control.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 21,
            'value' => true,
            'valuesAvailable' => [],
            'comment' => 'setting.control.footer.comment'],
        29 => [
            'name' => "setting.control.accessContent.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.control.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 22,
            'value' => false,
            'valuesAvailable' => [],
            'comment' => 'setting.control.accessContent.comment'],
        30 => [
            'name' => "setting.control.accessEmpty.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.control.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 23,
            'value' => false,
            'valuesAvailable' => [],
            'comment' => 'setting.control.accessEmpty.comment'],
        31 => [
            'name' => "setting.control.convocationContent.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.control.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 24,
            'value' => true,
            'valuesAvailable' => [],
            'comment' => 'setting.control.convocationContent.comment'],
        //------------------------------
        // GENERAL - URL SETTINGS
        //------------------------------
        32 => [
            'name' => "setting.general.url.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_ADMIN,
            'position' => 26,
            'value' => "https://cabanisation66.fr",
            'valuesAvailable' => [],
            'comment' => 'setting.general.url.comment'],
        33 => [
            'name' => "setting.general.urlGouv.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_ADMIN,
            'position' => 27,
            'value' => "https://www.pyrenees-orientales.gouv.fr/",
            'valuesAvailable' => [],
            'comment' => 'setting.general.urlGouv.comment'],
        34 => [
            'name' => "setting.general.prefixUsername.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 28,
            'value' => "lucca",
            'valuesAvailable' => [],
            'comment' => 'setting.general.prefixUsername.comment'],

        35 => [
            'name' => "setting.map.maxResults.name",
            'type' => Setting::TYPE_INTEGER,
            'category' => 'setting.category.map.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 29,
            'value' => 100,
            'valuesAvailable' => [],
            'comment' => 'setting.map.maxResults.comment'],
        36 => [
            'name' => "setting.general.ddtAcronym.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_ADMIN,
            'position' => 30,
            'value' => 'DDTM',
            'valuesAvailable' => ['DDT', 'DDTM'],
            'comment' => 'setting.general.ddtAcronym.comment'],
        37 => [
            'name' => "setting.general.catchphrase1.name",
            'type' => Setting::TYPE_TEXT,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_ADMIN,
            'position' => 31,
            'value' => 'Lutte contre la cabanisation',
            'valuesAvailable' => [],
            'comment' => 'setting.general.catchphrase1.comment'],
        38 => [
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
        39 => [
            'name' => "setting.module.mayorletter.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.module.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 33,
            'value' => false,
            'valuesAvailable' => [],
            'comment' => 'setting.module.mayorletter.comment'],
        40 => [
            'name' => "setting.module.dashboardAdmin.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.module.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 13,
            'value' => false,
            'valuesAvailable' => [],
            'comment' => 'setting.module.dashboardAdmin.comment'],

        //------------------------------
        // General URL converter PDF into JPEG
        //------------------------------
        41 => [
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
        42 => [
            'name' => "setting.module.annexes.name",
            'type' => Setting::TYPE_BOOL,
            'category' => 'setting.category.module.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 1,
            'value' => false,
            'valuesAvailable' => [],
            'comment' => 'setting.module.annexes.comment'],
        43 => [
            'name' => "setting.folder.annexesQuantity.name",
            'type' => Setting::TYPE_INTEGER,
            'category' => 'setting.category.folder.name',
            'accessType' => Setting::ACCESS_TYPE_SUPER_ADMIN,
            'position' => 2,
            'value' => 3,
            'valuesAvailable' => [],
            'comment' => 'setting.folder.annexesQuantity.comment'],
        45 => [
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
