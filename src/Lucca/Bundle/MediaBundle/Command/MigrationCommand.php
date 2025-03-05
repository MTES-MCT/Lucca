<?php

namespace Lucca\Bundle\MediaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class InitializationCommand
 *
 * @package Lucca\MediaBundle\Command
 * @author Alizee Meyer <alizee.m@numeric-wave.eu>
 */
class MigrationCommand extends ContainerAwareCommand
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * Configure command parameters
     */
    protected function configure()
    {
        $this
            ->setName('lucca:migration:media')
            ->setDescription('Migrate the media from the old logic to the new one.')
            ->setHelp('Migrate the media from the old logic to the new one');
    }

    /**
     * Initialize var for process from argument/option Command
     *
     * @param InputInterface $p_input
     * @param OutputInterface $p_output
     */
    protected function initialize(InputInterface $p_input, OutputInterface $p_output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();
    }

    /**
     * Execute command
     * Write log and start the migration
     *
     * @param InputInterface $p_input
     * @param OutputInterface $p_output
     * @return bool|int|null
     * @throws \Exception
     */
    protected function execute(InputInterface $p_input, OutputInterface $p_output)
    {
        $startTime = microtime(true);

        // Start command
        $now = new \DateTime('now');
        $p_output->writeln([
            '',
            'Initialize default configuration in Lucca',
            'Start: ' . $now->format('d-m-Y H:i:s') . '',
            '-----------------------------------------------',
        ]);

        // Command logic stored here
        $this->migrateMediaBundle($p_input, $p_output);

        $this->reformatContent($p_output);


        // Showing when the script is over
        $now = new \DateTime('now');
        $p_output->writeln([
            '',
            '-----------------------------------------------',
            'End: ' . $now->format('d-m-Y H:i:s') . '',
        ]);

        $finishTime = microtime(true);
        $elapsedTime = $finishTime - $startTime;
        $p_output->writeln([
            '',
            sprintf('<comment>[INFO] Migration ended: Elapsed time %.2f ms</comment>', $elapsedTime * 1000),
            '-----------------------------------------------',
        ]);

        return true;
    }

    /**
     * Migrate all the media
     *
     * @param InputInterface $p_input
     * @param OutputInterface $p_output
     */
    protected function migrateMediaBundle(InputInterface $p_input, OutputInterface $p_output)
    {
        $em = $this->em;

        // Turning off doctrine default logs queries for saving memory
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        /** Search default Category - if not create it */
        $medias = $em->getRepository('LuccaMediaBundle:Media')->findBy(array(
            'filePath' => null
        ));

        /** Get category and folder in DB for the broken medias */
        $categoryDefault = $this->em->getRepository('LuccaMediaBundle:Category')->findDefaultCategory();
        $folderDefault = $this->em->getRepository('LuccaMediaBundle:Folder')->findOneBy(array('enabled' => 1));
        $userDefault = $this->em->getRepository('LuccaUserBundle:User')->findOneBy(array(
            'username' => 'lucca-nw-01'
        ));

        foreach ($medias as $media) {

            /** First get the local path of the media */
            $pathDate = $media->getCreatedAt()->format('Y') . '/' . $media->getCreatedAt()->format('m');
            $path = $this->getContainer()->getParameter('media_old_path') . $pathDate . '/' . $media->getName();

            /** Then create file by using the path founded */
            try {
                $file = new File($path);
                $uploadedFile = new UploadedFile($path, $file->getBasename(), $file->getMimeType());

                /** Upload the file in order to use the new logic of naming and organisation */
                $media = $this->getContainer()->get('lucca.manager.file')->uploadFile($uploadedFile, $media, $media->getCreatedAt());

                $media->setEnabled(true);
                $media->setPublic(false);

                $em->persist($media);
            } catch (\Exception $exception) {
                $p_output->writeln(
                    [
                        sprintf('<comment>[WARNING] : File at path' . $path . ' for the media id ' . $media->getId() . ' doesn\'t exist</comment> Please check this media. Temporary data are set for it and enabled is set to false'),
                    ]
                );

                $media->setNameOriginal($media->getName());
                $media->setNameCanonical($media->getName());
                $media->setCategory($categoryDefault);
                $media->setFolder($folderDefault);
                $media->setOwner($userDefault);
                $media->setDescription("Migration failed please check this media. File was not in media folder during the migration.");
                $media->setFilePath('');
                $media->setMimeType('');
                $media->setEnabled(true);
                $media->setPublic(false);
            }
        }


        /** flush last items, detach all for doctrine and finish progress */
        $em->flush();
        $em->clear();
    }

    /** This function is used to reformat all old file path */
    protected function reformatContent($p_output)
    {
        /******************************* Editions ******************************************************************/
        /** Get services used for the next step */
        $pathFormatter = $this->getContainer()->get('lucca.utils.formatter.media_path');
        $em = $this->em;
        $batchSize = 10;
        $i = 1;

        /** Get all control editions */
        $editions = $em->getRepository('LuccaMinuteBundle:ControlEdition')->findAll();

        /** Create progress bar */
        $progress = new ProgressBar($p_output, count($editions));
        $progress->start();

        /** For each edition change old media path to use the new one */
        /** Example : /media/2020/11/image.jpg -> /media/show/image-jpg */
        foreach ($editions as $edition) {
            if ($edition->getLetterConvocation()) {
                $edition->setLetterConvocation($pathFormatter->formatOldPath($edition->getLetterConvocation()));
            }
            if ($edition->getLetterAccess()) {
                $edition->setLetterAccess($pathFormatter->formatOldPath($edition->getLetterAccess()));
            }
            $em->persist($edition);

            /** Update progress bar */
            if (($i % $batchSize) === 0) {
                $em->flush();
                $progress->advance($batchSize);
            }
            $i++;
        }
        $em->flush();
        $em->clear();

        $p_output->writeln(sprintf('<comment>[INFO] End of control edition migration</comment>'));

        /********* Get all courrier editions **********/
        $editions = $em->getRepository('LuccaMinuteBundle:CourierEdition')->findAll();

        /** Create progress bar */
        $progress = new ProgressBar($p_output, count($editions));
        $progress->start();

        /** For each edition change old media path to use the new one */
        /** Example : /media/2020/11/image.jpg -> /media/show/image.jpg */
        foreach ($editions as $edition) {
            if ($edition->getLetterDdtm()) {
                $edition->setLetterDdtm($pathFormatter->formatOldPath($edition->getLetterDdtm()));
            }
            if ($edition->getLetterJudicial()) {
                $edition->setLetterJudicial($pathFormatter->formatOldPath($edition->getLetterJudicial()));
            }
            $em->persist($edition);

            /** Update progress bar */
            if (($i % $batchSize) === 0) {
                $em->flush();
                $progress->advance($batchSize);
            }
            $i++;
        }
        $em->flush();
        $em->clear();
        $p_output->writeln(sprintf('<comment>[INFO] End of courier edition migration</comment>'));

        /************* Get all folder editions *************/
        $editions = $em->getRepository('LuccaMinuteBundle:FolderEdition')->findAll();

        /** Create progress bar */
        $progress = new ProgressBar($p_output, count($editions));
        $progress->start();

        /** For each edition change old media path to use the new one */
        /** Example : /media/2020/11/image.jpg -> /media/show/image.jpg */
        foreach ($editions as $edition) {
            if ($edition->getFolderVersion()) {
                $edition->setFolderVersion($pathFormatter->formatOldPath($edition->getFolderVersion()));
                $em->persist($edition);
            }
            /** Update progress bar */
            if (($i % $batchSize) === 0) {
                $em->flush();
                $progress->advance($batchSize);
            }
            $i++;
        }
        $em->flush();
        $em->clear();
        $p_output->writeln(sprintf('<comment>[INFO] End of folder edition migration</comment>'));

        /*********** Get all courier human editions *************/
        $editions = $em->getRepository('LuccaMinuteBundle:CourierHumanEdition')->findAll();

        /** Create progress bar */
        $progress = new ProgressBar($p_output, count($editions));
        $progress->start();

        /** For each edition change old media path to use the new one */
        /** Example : /media/2020/11/image.jpg -> /media/show/image.jpg */
        foreach ($editions as $edition) {
            if ($edition->getLetterOffender()) {
                $edition->setLetterOffender($pathFormatter->formatOldPath($edition->getLetterOffender()));
            }
            if ($edition->getLetterOffenderEdited()) {
                $edition->setLetterOffenderEdited($pathFormatter->formatOldPath($edition->getLetterOffenderEdited()));
            }
            $em->persist($edition);
            /** Update progress bar */
            if (($i % $batchSize) === 0) {
                $em->flush();
                $progress->advance($batchSize);
            }
            $i++;
        }
        $em->flush();
        $em->clear();
        $p_output->writeln(sprintf('<comment>[INFO] End of courier human edition migration</comment>'));

        /******************************* Html fields *****************************************************************/

        /************* Get all pages ************/
        $pages = $em->getRepository('LuccaContentBundle:Page')->findAll();

        /** Create progress bar */
        $progress = new ProgressBar($p_output, count($pages));
        $progress->start();

        /** For each edition change old media path to use the new one */
        /** Example : /media/2020/11/image.jpg -> /media/show/image.jpg */
        foreach ($pages as $item) {
            if ($item->getContent()) {
                $item->setContent($pathFormatter->formatOldPath($item->getContent(), true));
                $em->persist($item);
            }
            /** Update progress bar */
            if (($i % $batchSize) === 0) {
                $em->flush();
                $progress->advance($batchSize);
            }
            $i++;
        }
        $em->flush();
        $em->clear();
        $p_output->writeln(sprintf('<comment>[INFO] End of page migration</comment>'));

        /************ Get all folders **************/
        $rangeIds = 50;
        $startId = 0;
        $endId = $startId + $rangeIds;
        $folders = $em->getRepository('LuccaMinuteBundle:Folder')->findAllBetweenId($startId, $endId);

        while (count($folders) > 0) {
            /** Create progress bar */
            $progress = new ProgressBar($p_output, count($folders));
            $progress->start();

            /** For each edition change old media path to use the new one */
            /** Example : /media/2020/11/image.jpg -> /media/show/image.jpg */
            foreach ($folders as $item) {
                if ($item->getAscertainment())
                    $item->setAscertainment($pathFormatter->formatOldPath($item->getAscertainment()));
                if ($item->getDetails())
                    $item->setDetails($pathFormatter->formatOldPath($item->getDetails()));
                if ($item->getViolation())
                    $item->setViolation($pathFormatter->formatOldPath($item->getViolation()));
                $em->persist($item);
                /** Update progress bar */
                if (($i % $batchSize) === 0) {
                    $em->flush();
                    $progress->advance($batchSize);
                }
                $i++;
            }
            $em->flush();
            $em->clear();
            /** Get the next range of folders */
            $startId = $endId + 1;
            $endId = $startId + $rangeIds;
            /** Get next folders */
            $folders = $em->getRepository('LuccaMinuteBundle:Folder')->findAllBetweenId($startId, $endId);
            $p_output->writeln(sprintf('<comment>[INFO] ---- End step of folder migration</comment>'));
        }
        $p_output->writeln(sprintf('<comment>[INFO] End of folder migration</comment>'));

        /************ Get all courier **************/
        $courier = $em->getRepository('LuccaMinuteBundle:Courier')->findAll();

        /** Create progress bar */
        $progress = new ProgressBar($p_output, count($courier));
        $progress->start();

        /** For each edition change old media path to use the new one */
        /** Example : /media/2020/11/image.jpg -> /media/show/image.jpg */
        foreach ($courier as $item) {
            if ($item->getContext())
                $item->setContext($pathFormatter->formatOldPath($item->getContext()));
            $em->persist($item);
            /** Update progress bar */
            if (($i % $batchSize) === 0) {
                $em->flush();
                $progress->advance($batchSize);
            }
            $i++;
        }
        $em->flush();
        $em->clear();
        $p_output->writeln(sprintf('<comment>[INFO] End of courier migration</comment>'));

        /************ Get all updating **************/
        $updating = $em->getRepository('LuccaMinuteBundle:Updating')->findAll();

        /** Create progress bar */
        $progress = new ProgressBar($p_output, count($updating));
        $progress->start();

        /** For each edition change old media path to use the new one */
        /** Example : /media/2020/11/image.jpg -> /media/show/image.jpg */
        foreach ($updating as $item) {
            if ($item->getDescription())
                $item->setDescription($pathFormatter->formatOldPath($item->getDescription()));
            $em->persist($item);
            /** Update progress bar */
            if (($i % $batchSize) === 0) {
                $em->flush();
                $progress->advance($batchSize);
            }
            $i++;
        }
        $em->flush();
        $em->clear();
        $p_output->writeln(sprintf('<comment>[INFO] End of updating migration</comment>'));
    }
}
