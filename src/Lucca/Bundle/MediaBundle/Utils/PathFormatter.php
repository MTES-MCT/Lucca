<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Utils;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;

use Lucca\Bundle\MediaBundle\Entity\Media;

readonly class PathFormatter
{
    public function __construct(
        private EntityManagerInterface $em,
        private RouterInterface        $router,
        private ParameterBagInterface  $params,
    )
    {
    }

    /**
     * Format the text given to change media path depend on file targeted (PDF or export)
     */
    public function formatText(string $text, string $target = "PDF", string $newPath = null): array|string
    {
        /** Store the text in temp variable */
        $formattedText = $text;

        /** Get a random media in order to get dynamically the web path */
        $randomMedia = $this->em->getRepository(Media::class)->findFirstMedia();

        /** First generate a route for a random media in order to get the relative web path */
        $randomWebPath = $this->router->generate('lucca_media_show', array('p_fileName' => $randomMedia->getNameCanonical()));

        /** Remove random media from path */
        $relativeWebPath = str_replace($randomMedia->getNameCanonical(), "", $randomWebPath);

        /** Explode the locales authorized to be able to iterate on it */
        $languages = explode("|", $this->params->get('locales_authorized'));
        $firstLanguage = "";
        /** First loop to find what is the language of the relative path */
        foreach ($languages as $language) {
            /** Check if the relative path contain the current language, if yes set the current language to this one */
            if (str_contains($randomWebPath, "" . $language . "")) {
                $firstLanguage = $language;
                break;
            }
        }
        $prevLanguage = $firstLanguage;
        /** Second loop to change all the file path that can have a different languages */
        foreach ($languages as $language) {
            /** Replace the language in relative path to search for path in all language */
            $relativeWebPath = str_replace($prevLanguage, $language, $relativeWebPath);
            /** Format text based on the current language */
            $formattedText = $this->formatOneLanguage($formattedText, $relativeWebPath, $target, $newPath);

            if (!str_contains($relativeWebPath, '/app_dev.php')) {
                $relativeWebPath = '/app_dev.php' . $relativeWebPath;
            } else {
                $relativeWebPath = str_replace('/app_dev.php', '', $relativeWebPath);
            }
            $formattedText = $this->formatOneLanguage($formattedText, $relativeWebPath, $target, $newPath);

            /** Set the prevLanguage to the current one in order to replace ir in the next iteration */
            $prevLanguage = $language;
        }

        return $formattedText;
    }

    /**
     * This function will reformat text based on $p_relativeWebPath
     * This function is split in order to reformat text for all possible locales
     */
    private function formatOneLanguage(string $formattedText, $relativeWebPath, string $target = "PDF", string $newPath = null): array|string
    {
        /** Get the first mediaName */
        /** Quotation mark is used to get the string between path and and of the name file */
        /** Html separator */
        $mediaName = $this->getBetween($formattedText, "\"" . $relativeWebPath, "\"");

        /** Remove width parameter that we can find in show media URL */
        $mediaName = explode("?width=", $mediaName)[0];

        /** We replace old path by the new one as long as we can find a mediaName */
        while ($mediaName != '') {
            /** If the target is a PDF we need to get local path */
            /** If the target is an export we need to get web path with domain name */
            if ($target == "PDF") {
                $media = $this->em->getRepository(Media::class)->findOneFileByName($mediaName);
                /** Change the file for a local path
                 * Example:
                 * faerun/media/show/614b1fd613079_selection-002-png -> /srv/docs/uploads/2021/38/614b1fd613079_selection-002-png
                 */
                $formattedText = str_replace($relativeWebPath . $mediaName, $newPath . $media->getFilePath(), $formattedText);
            } else if ($target == "export") {

                /** Generate the absolute web path of the media */
                $absoluteWebPath = $this->router->generate('lucca_media_show', array('p_fileName' => $mediaName), UrlGenerator::ABSOLUTE_URL);
                /** Change the file for an absolute web path
                 * Example:
                 * faerun/media/show/614b1fd613079_selection-002-png -> https://lucca.local/fr/faerun/media/show/614b1fd613079_selection-002-png
                 */
                $formattedText = str_replace($relativeWebPath . $mediaName, $absoluteWebPath, $formattedText);
            }

            /** Get the next media name */
            /** Html separator */
            $mediaName = $this->getBetween($formattedText, "\"" . $relativeWebPath, "\"");
        }

        return $formattedText;
    }

    /**
     * This function will reformat old media path to use the new naming
     * This will be use only one time from the migration command
     *
     * Also if this function is called for a public entity, it will set the field public of the media to true
     */
    public function formatOldPath(string $p_formattedText, ?bool $p_isPublic = null): array|string
    {
        /** Old media path is write in code because this function will be used only 1 time */
        $startOldMediaPath = "/media/20";

        /** Get the old path based on the string defined earlier */
        $oldPath = $this->getBetween($p_formattedText, $startOldMediaPath, "\"");

        /** While we can find an old path continue to replace it */
        while ($oldPath) {
            /** Concatenate string in order to get the complete path */
            $oldPathComplete = $startOldMediaPath . $oldPath;

            /** Explode ths string at each / in order to be able to get the name of the file after the last slash */
            $tempArray = explode("/", $oldPathComplete);

            /** If explode is not null it mean that we have a path to update */
            if ($tempArray) {

                /** Get the media name after the last slash */
                $mediaOldName = $tempArray[count($tempArray) - 1];
                /** Find the media associated to the old name */
                $media = $this->em->getRepository(Media::class)->findOneBy(array(
                    'nameOriginal' => $mediaOldName
                ));

                /** Use the execution of this command to set public to media that are used in public entity */
                if ($p_isPublic) {
                    $media->setPublic(true);
                    $this->em->persist($media);
                }

                /** Generate the relative web path for this media */
                /** We use the relative path in order to avoid error with domain name */
                $webPath = $this->router->generate('lucca_media_show', array('p_fileName' => $media->getNameCanonical()));

                /** Replace the old path in the text */
                $p_formattedText = str_replace($oldPathComplete, $webPath, $p_formattedText);

            }
            /** Find the next old path */
            $oldPath = $this->getBetween($p_formattedText, $startOldMediaPath, "\"");
        }

        return $p_formattedText;
    }

    /**
     * This function will reformat broken media path to use the web path
     * These function and the previous one are not optimized because it will be used only one time
     */
    public function formatBrokenPath(string $formattedText, ?bool $isPublic = null, ?string $path = null, string $defaultFileName = 'pref-herault', int $defaultId = 45): array|string
    {
        /** Old media path is write in code because this function will be used only 1 time */
        $startOldMediaPath = "/media/20";
        if ($path != null) {
            $startOldMediaPath = $path;
        }

        /** Get the old path based on the string defined earlier */
        $oldPath = $this->getBetween($formattedText, $startOldMediaPath, "\"");

        /** While we can find an old path continue to replace it */
        while ($oldPath) {
            /** Concatenate string in order to get the complete path */
            $oldPathComplete = $startOldMediaPath . $oldPath;

            /** Explode ths string at each / in order to be able to get the name of the file after the last slash */
            $tempArray = explode("/", $oldPathComplete);

            /** If explode is not null it mean that we have a path to update */
            if ($tempArray) {

                /** Get the media name after the last slash */
                $mediaOldName = $tempArray[count($tempArray) - 1];
                /** This function has been modified in order to ix broken path in lucca 34 */
                $mediaOldName = str_replace('?width=100', '', $mediaOldName);
                /** This function has been modified in order to ix broken path in lucca 34 */

                $media = $this->em->getRepository(media::class)->findOneBy(array(
                    'nameCanonical' => $mediaOldName
                ));

                /** If we can't find the media with the canonical name find a media that match the name */
                /** All information are not set with var because it will be used only one time */
                if ($media == null) {
                    if (str_contains($mediaOldName, $defaultFileName)) {
                        $media = $this->em->getRepository(Media::class)->find($defaultId);
                    }
                }

                if($media == null){
                    continue;
                }

                /** Use the execution of this command to set public to media that are used in public entity */
                if ($isPublic) {
                    $media->setPublic(true);
                    $this->em->persist($media);
                }

                /** Generate the relative web path for this media */
                /** We use the relative path in order to avoid error with domain name */
                $webPath = $this->router->generate('lucca_media_show', array('p_fileName' => $media->getNameCanonical()));

                /** Replace the old path in the text */
                $formattedText = str_replace($oldPathComplete, $webPath, $formattedText);

            }
            /** Find the next old path */
            $oldPath = $this->getBetween($formattedText, $startOldMediaPath, "\"");
        }

        return $formattedText;
    }

    /**
     * This function is used to find string between 2 other strings
     */
    private function getBetween($content, $start, $end): string
    {
        /** Split the string at each string $start */
        $result = explode($start, $content);

        /** Get the first result */
        if (isset($result[1])) {
            /** Split the string at the string $end */
            /** Get the first string of the result in order to have the first string between $start and $end */
            $result = explode($end, $result[1]);

            return $result[0];
        }

        /** If the start can't be found return empty string */
        return '';
    }

    public function getName(): string
    {
        return 'lucca.utils.formatter.media_path';
    }
}
