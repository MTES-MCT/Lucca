<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Twig;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

use Lucca\Bundle\MediaBundle\Entity\{Gallery, Media};
use Lucca\Bundle\MediaBundle\Manager\GalleryForPdfManager;
use Lucca\Bundle\MediaBundle\Utils\{FormatDecisionMaker, PathFormatter};

/**
 * Class MediaExtension
 * TODO clean and refactor the switch methode
 * TODO merge functions mediaPdf and mediaTag
 */
class MediaExtension extends AbstractExtension
{
    public function __construct(
        private readonly RouterInterface      $router,
        private readonly FormatDecisionMaker  $formatDecisionMaker,
        private readonly GalleryForPdfManager $galleryForPdf,
        private readonly PathFormatter        $pathFormatter,

        #[Autowire(param: 'lucca_media.upload_directory')]
        private readonly string               $upload_dir,
    )
    {
    }

    /**
     * Get twig filters
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('mediaTag', [$this, 'mediaTagFormatterByMime'], ['is_safe' => ['html']]),
            new TwigFilter('mediaPdf', [$this, 'mediaPdfFormatter'], ['is_safe' => ['html']]),
            new TwigFilter('galleryPdf', [$this, 'galleryPdfFormatter'], ['is_safe' => ['html']]),
            new TwigFilter('localPath', [$this, 'localPathFormatter'], ['is_safe' => ['html']]),
            new TwigFilter('localPathSimple', [$this, 'localPathSimpleFormatter'], ['is_safe' => ['html']]),
            new TwigFilter('mediaPdfPath', [$this, 'mediaPdfPath'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Display file with the correct format or embed object
     */
    public function mediaTagFormatterByMime(?Media $media = null, $width = null, $class = null, $absolutePath = null): ?string
    {
        if (!$media) {
            return null;
        }

        $fileType = $this->formatDecisionMaker->getFileTypeByMime($media->getMimeType());
        $mediaTag = null;

        $filePath = $this->buildFilePath($media, $width, $absolutePath);

        /** TODO try to get file type with extension if mime is not set correctly */
//        if (!$fileType && $fileType == "wildcard") {
//            $extensionCategory = $this->formatDecisionMaker->getFileTypeByExtension(pathinfo($media->getFilePath()));
//        }

        switch ($fileType) {
            case 'image':
                $mediaTag = $this->getImageTag($media, $width, $class, $absolutePath);
                break;

            case 'audio':
                $mediaTag = '<figure><audio controls class="' . $class . '" src="' . $filePath . '"> Your browser does not support the <code>audio</code> element.</audio></figure>';
                break;

            case 'video':
                $mediaTag = '<video controls><source class="' . $class . '" src="' . $filePath . '" type="' . $media->getMimeType() . '">Sorry, your browser doesn\'t support embedded videos.</video>';
                break;

            case 'font':
                $icon = "fas fa-font";
                break;

            case 'text_document':
                $icon = "fas fa-file-word";
                break;

            case 'sheet_document':
                $icon = "fas fa-file-excel";
                break;

            case 'presentation_document':
                $icon = "fas fa-file-powerpoint";
                break;

            case 'archive':
                $icon = "fas fa-file-archive";
                break;

            case 'media-file-container':
                $icon = "fas fa-image-alt";
                break;

            default :
                $icon = "fas fa-file-alt";
                break;
        }

        /** If media cannot be displayed with as specific render - take icon and common display to create mediaTag */
        if (isset($icon) and $icon !== null and !isset($mediaTag)) {
            $mediaTag = '<div class="media-file-container ' . $class . '"><a href="' . $filePath . '" target="_blank"><i class="' . $icon . ' fa-2x"></i> ' . $media->getName() . '</a></div>';
        }

        return $mediaTag;
    }

    /**
     * Display file with the correct format or embed object into a PDF view
     */
    public function mediaPdfFormatter(?Media $media = null, $width = null, $class = null, $absolutePath = null): ?string
    {
        if (!$media) {
            return null;
        }

        $fileType = $this->formatDecisionMaker->getFileTypeByMime($media->getMimeType());
        $mediaTag = null;

        $filePath = $this->buildFilePath($media, $width, $absolutePath);

        /** TODO try to get file type with extension if mime is not set correctly */
//        if (!$fileType && $fileType == "wildcard") {
//            $extensionCategory = $this->formatDecisionMaker->getFileTypeByExtension(pathinfo($media->getFilePath()));
//        }

        switch ($fileType) {
            case 'image':
                $mediaTag = $this->getImagePdf($media, $width, $class);
                break;

            case 'audio':
                $icon = "fas fa-image-audio";
                break;

            case 'video':
                $icon = "fas fa-image-video";
                break;

            case 'font':
                $icon = "fas fa-font";
                break;

            case 'text_document':
                $icon = "fas fa-image-word";
                break;

            case 'sheet_document':
                $icon = "fas fa-table";
                break;

            case 'presentation_document':
                $icon = "fas fa-image-powerpoint";
                break;

            case 'archive':
                $icon = "fas fa-image-archive";
                break;

            case 'media-file-container':
                $icon = "fas fa-image-alt";
                break;

            default :
                $icon = "fas fa-image";
                break;
        }

        /** If media cannot be displayed with as specific render - take icon and common display to create mediaTag */
        if (isset($icon) and $icon !== null and !isset($mediaTag)) {
            $mediaTag = '<div class="media-file-container ' . $class . '"><a href="' . $filePath . '" target="_blank"><i class="' . $icon . ' fa-2x"></i> ' . $media->getName() . '</a></div>';
        }

        return $mediaTag;
    }

    /**
     * Return a simple local path to a media
     */
    public function mediaPdfPath(?Media $media = null): ?string
    {
        /** Change the file web path for a local path
         * Example:
         * http://lucca.code/app_dev.php/media/show/61af755035d80_le-plan-local-durbanisme-1248x703-jpg -> /var/www/html/lucca.numeric-wave.io/../lucca.numeric-wave.doc/Media/2021/49/61af755035d80_le-plan-local-durbanisme-1248x703-jpg
         */
        return $this->upload_dir . $media->getFilePath();
    }

    /**
     * Build HTML view with a gallery of images
     */
    public function galleryPdfFormatter(Gallery $gallery , $width = null, $class = null): string
    {
        $aMediasImages = $this->galleryForPdf->manageGalleryForPdf($gallery);

        $mediaTag = '';
        foreach ($aMediasImages as $media) {
            $mediaTag .= $this->getImagePdf($media, $width, $class);
        }

        return $mediaTag;
    }

    /**
     * Build HTML view of an image
     */
    public function getImageTag(Media $media, $width = null, $class = null, $absolutePath = null): string
    {
        $filePath = $this->buildFilePath($media, $width, $absolutePath);
        $metas = $media->getMetas();
        $attributes = "";
        $classes = "img-fluid";

        foreach ($metas as $meta) {
            $attributes .= ' ' . $meta->getKeyword() . '="' . $meta->getValue() . '"';
        }

        if ($class) {
            $classes = $class;
        }

        return '<img class="' . $classes . '" ' . $attributes . ' src="' . $filePath . '" />';
    }

    /**
     * Build file path with params and absolute param
     */
    private function buildFilePath(Media $media, $width = null, $absolutePath = null): string
    {
        $options = array('p_fileName' => $media->getNameCanonical());
        $absolutePath = UrlGeneratorInterface::ABSOLUTE_URL;

        if ($absolutePath !== null && $absolutePath) {
            $absolutePath = UrlGeneratorInterface::ABSOLUTE_URL;
        }

        if ($width !== null) {
            $options['width'] = $width;
        }

        return $this->router->generate('lucca_media_show', $options, $absolutePath);
    }

    /**
     * Build HTML view of an image display into a PDF view
     */
    public function getImagePdf(Media $media, $width = null, $class = null): string
    {
        $url = $this->upload_dir . '/' . $media->getFilePath();

        if ($width !== null) {
            $url .= '?width=' . $width;
        }

        $metas = $media->getMetas();
        $attributes = "";
        $classes = "img-fluid";

        foreach ($metas as $meta) {
            $attributes .= ' ' . $meta->getKeyword() . '="' . $meta->getValue() . '"';
        }

        if ($class) {
            $classes = $class;
        }

        return '<img class="' . $classes . '" ' . $attributes . ' src="' . $url . '" >';
    }

    /**
     * Change all path to have only local path for all media in html (Used in PDF)
     */
    public function localPathFormatter(string $p_text): string
    {
        return $this->pathFormatter->formatText($p_text, "PDF", $this->upload_dir);
    }
}
