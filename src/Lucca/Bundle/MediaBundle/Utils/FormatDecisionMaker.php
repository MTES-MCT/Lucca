<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Utils;

class FormatDecisionMaker
{
    /**
     * List of Mime saved by Lucca
     */
    private static array $mimes = [
        "audio" => [
            "audio/aac", "audio/midi", "audio/mp3", "audio/mpeg", "audio/x-wav", "audio/webm", "audio/3gpp2", "audio/3gpp", "audio/ogg", "application/ogg"
        ],
        "video" => [
            "video/x-msvideo", "video/mpeg", "video/ogg", "video/webm", "video/3gpp", "video/3gpp2", "application/ogg"
        ],
        "image" => [
            "image/gif", "image/png", "image/x-icon", "image/jpeg", "image/svg+xml", "image/tiff", "image/webp", "image/svg"
        ],
        "font" => [
            "font/otf", "font/ttf", "font/woff", "font/woff2", "application/vnd.ms-fontobject"
        ],
        "text_document" => [
            "application/x-abiword", "application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/vnd.oasis.opendocument.text",
            "application/rtf", "text/plain"
        ],
        "sheet_document" => [
            "application/vnd.oasis.opendocument.spreadsheet", "application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
        ],
        "presentation_document" => [
            "application/vnd.oasis.opendocument.presentation", "application/vnd.ms-powerpoint", "application/vnd.openxmlformats-officedocument.presentationml.presentation"
        ],
        "archive" => [
            "application/x-bzip", "application/x-bzip2", "application/x-rar-compressed", "application/x-tar", "application/zip", "application/x-7z-compressed", "application/java-archive"
        ],
        "ebook" => [
            "application/vnd.amazon.ebook", "application/epub+zip"
        ],
        "other" => [
            "application/x-csh", "text/css", "text/csv", "text/html", "text/calendar", "application/javascript", "application/json", "application/vnd.apple.installer+xml", "application/pdf",
            "application/x-sh", "application/x-shockwave-flash", "application/typescript", "application/vnd.visio", "application/xhtml+xml", "application/xml", "application/vnd.mozilla.xul+xml",
        ],
        "wildcard" => [
            "application/octet-stream"
        ],
    ];

    /**
     * TODO add extensions for other file types
     */
    private static array $extensions = [
        "audio" => [
            "mp3", "wav", "aac", "mid", "mpa", "wma"
        ],
        "image" => [
            "jpg", "jpeg", "png", "psd", "pspimage", "tga", "thm", "tif", "tiff", "yuv", "ai", "eps", "ps", "svg", "indd"
        ],
        "font" => [
            "abf", "eot", "otf", "woff", "woff2", "tif", "tiff", "svg"
        ],
    ];

    public function getFileTypeByMime($p_mimeType): string
    {
        $mimeTypes = self::$mimes;
        $fileType = null;

        foreach ($mimeTypes as $mimeCategory => $mimeType) {

            if (in_array($p_mimeType, $mimeType, true)) {
                $fileType = $mimeCategory;
                break;
            }
        }

        return $fileType;
    }

    public function getFileTypeByExtension($p_extension): string
    {
        $extensions = self::$extensions;
        $fileType = null;

        foreach ($extensions as $extensionCategory => $extension) {

            if (in_array($p_extension, $extension, true)) {
                $fileType = $extensionCategory;
                break;
            }
        }

        return $fileType;
    }

    /**
     * Get all Mimes registered with a specific index
     */
    public function getMimes($index): array|false
    {
        if (self::$mimes[$index]) {
            return self::$mimes[$index];
        }

        return false;
    }

    /**
     * Get all Extensions registered with a specific index
     */
    public function getExtensions($index): array|false
    {
        if (self::$extensions[$index]) {
            return self::$extensions[$index];
        }

        return false;
    }
}
