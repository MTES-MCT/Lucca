<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ContentBundle\Command;

use DateTime;
use Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Lucca\Bundle\ContentBundle\Entity\{Area, Page, SubArea};
use Lucca\Bundle\CoreBundle\Utils\Canonalizer;

class InitializationCommand extends Command
{
    public function __construct(
        private readonly ObjectManager $om,
        private readonly Canonalizer $canonalizer,
    )
    {
        parent::__construct();
    }

    /**
     * Configure command parameters
     */
    protected function configure(): void
    {
        $this
            ->setName('lucca:init:content')
            ->setDescription('Initialize ContentBundle with default entities.')
            ->setHelp('Initialize ContentBundle with default areas, sub-areas and pages');
    }

    /**
     * Execute command
     * Write log and start the import
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $startTime = microtime(true);

        // Start command
        $now = new DateTime('now');
        $output->writeln([
            '',
            'Initialize default ContentBundle configuration in Lucca',
            'Start: ' . $now->format('d-m-Y H:i:s'),
            '-----------------------------------------------',
        ]);

        // Command logic stored here
        $this->initContentBundle($input, $output);

        // Showing when the script is over
        $now = new DateTime('now');
        $output->writeln([
            '',
            '-----------------------------------------------',
            'End: ' . $now->format('d-m-Y H:i:s'),
        ]);

        $finishTime = microtime(true);
        $elapsedTime = $finishTime - $startTime;
        $output->writeln([
            '',
            sprintf('<comment>[INFO] Import stored in database: Elapsed time %.2f ms</comment>', $elapsedTime * 1000),
            '-----------------------------------------------',
        ]);

        return 1;
    }

    /**
     * Import from file and create objects
     *
     * @throws NonUniqueResultException
     */
    protected function initContentBundle(InputInterface $input, OutputInterface $output): void
    {
        // Turning off doctrine default logs queries for saving memory
        $this->om->getConnection()->getConfiguration()->setSQLLogger(null);

        $areas = [
            ['name' => 'Zone principale', 'position' => 'choice.position.content'],
            ['name' => 'Pied de page', 'position' => 'choice.position.footer'],
            ['name' => 'Administration', 'position' => 'choice.position.admin'],
        ];

        $subAreas = [
            ['name' => 'Comprendre la cabanisation', 'position' => '1', 'color' => '#f05050', 'width' => 'col-lg-4 col-md-4 col-sm-6 col-xs-12', 'area' => 'choice.position.content', 'code' => 'comprendre-la-cabanisation-1'],
            ['name' => 'Mobilisation et actions ', 'position' => '2', 'color' => '#826969', 'width' => 'col-lg-4 col-md-4 col-sm-6 col-xs-12' , 'area' => 'choice.position.content', 'code' => 'mobilisation-et-actions-2'],
            ['name' => 'Dispositions pénales ', 'position' => '4', 'color' => '#28d2eb', 'width' => 'col-lg-4 col-md-4 col-sm-6 col-xs-12' , 'area' => 'choice.position.content', 'code' => 'dispositions-penales-3'],

            ['name' => 'Aide', 'position' => '1', 'color' => '#ecff29', 'width' => 'col-lg-4 col-md-4 col-sm-6 col-xs-12' , 'area' => 'choice.position.footer', 'code' => 'aide-4'],
            ['name' => 'Légal', 'position' => '2', 'color' => '#ff9a3d', 'width' => 'col-lg-4 col-md-4 col-sm-6 col-xs-12' , 'area' => 'choice.position.footer', 'code' => 'legal-5'],

            ['name' => 'Documentation', 'position' => '2', 'color' => '#ff902b', 'width' => 'col-lg-4 col-md-4 col-sm-6 col-xs-12' , 'area' => 'choice.position.admin', 'code' => 'documentation-6'],
            ['name' => 'Utilisation de Lucca', 'position' => '3', 'width' => 'col-lg-3 col-md-4 col-sm-6 col-12' , 'color' => '#4EBEE6', 'area' => 'choice.position.admin', 'code' => 'utilisation-de-lucca-7'],
        ];

        $pages = [
            ['position' => '1', 'name' => 'Définition', 'subarea' => 'comprendre-la-cabanisation-1', 'author' => 'Terence'],
            ['position' => '2', 'name' => 'Enjeux', 'subarea' => 'comprendre-la-cabanisation-1', 'author' => 'Terence'],

            ['position' => '1', 'name' => 'FAQ', 'subarea' => 'aide-4', 'author' => 'Terence'],
            ['position' => '2', 'name' => 'Glossaire', 'subarea' => 'aide-4', 'author' => 'Terence'],
            ['position' => '3', 'name' => 'Contact', 'subarea' => 'aide-4', 'author' => 'Terence'],

            ['position' => '1', 'name' => 'Mentions légales ', 'subarea' => 'legal-5', 'author' => 'Terence'],
            ['position' => '2', 'name' => 'Conditions d utilisation ', 'subarea' => 'legal-5', 'author' => 'Terence'],
            ['position' => '3', 'name' => 'Crédits ', 'subarea' => 'legal-5', 'author' => 'Terence'],

            ['position' => '1', 'name' => 'Ouvrir un dossier', 'subarea' => 'utilisation-de-lucca-7', 'author' => 'Terence'],
            ['position' => '2', 'name' => 'Déclarer un contrôle', 'subarea' => 'utilisation-de-lucca-7', 'author' => 'Terence'],
        ];

        foreach ($areas as $area) {
            /** Search Area - if it does not exists, then create it - if it is disabled then enable it */
            $existingArea = $this->om->getRepository(Area::class)->findOneBy(["position" => $area["position"]]);

            if (!$existingArea){
                $newArea = new Area();
                $newArea->setName($area["name"]);
                $newArea->setPosition($area["position"]);
                $this->om->persist($newArea);

                $output->writeln($area["name"] . ' Created');
            } elseif (!$existingArea->isEnabled()){
                $existingArea->enable();
                $this->om->persist($existingArea);
                $output->writeln($area["name"] . ' Enabled');
            } else {
                $output->writeln( $area["name"] . ' already exists !');
            }
        }
        /** flush last items, detach all for doctrine and finish progress */
        $this->om->flush();
        $this->om->clear();
        foreach ($subAreas as $subArea) {
            /** Search Area - if it does not exists, then create it - if it is disabled then enable it */
            $existingSubArea = $this->om->getRepository(SubArea::class)->findOneBy(array("code" => $subArea["code"]));
            $linkedArea = $this->om->getRepository(Area::class)->findOneBy(array("position" => $subArea["area"]));

            if (!$existingSubArea) {
                $newSubArea = new SubArea();
                $newSubArea->setName($subArea["name"]);
                $newSubArea->setPosition($subArea["position"]);
                $newSubArea->setColor($subArea["color"]);
                $newSubArea->setWidth($subArea["width"]);
                $newSubArea->setTitle($subArea["name"]);
                $newSubArea->setArea($linkedArea);
                $newSubArea->setCode($subArea["code"]);

                $this->om->persist($newSubArea);

                $output->writeln($subArea["name"] . ' Created');
            } elseif (!$existingSubArea->isEnabled()) {
                $existingSubArea->enable();
                $this->om->persist($existingSubArea);
                $output->writeln($subArea["name"] . ' Enabled');
            } else {
                $output->writeln( $subArea["name"] . ' already exists !');
            }
        }

        /** flush last items, detach all for doctrine and finish progress */
        $this->om->flush();
        $this->om->clear();

        foreach ($pages as $page) {
            /** Search Area - if it does not exists, then create it - if it is disabled then enable it */
            $existingPage = $this->om->getRepository(Page::class)->findOneBy(["name" => $page["name"]]);
            $linkedSubArea = $this->om->getRepository(SubArea::class)->findOneBy(["code" => $page["subarea"]]);
            $author = $this->om->getRepository('LuccaUserBundle:User')->findOneBy(["username" => $page["author"]]);

            if (!$existingPage) {
                $newPage = new Page();
                $newPage->setName($page["name"]);
                $slugifiedName = $this->canonalizer->slugify($newPage->getName());
                $newPage->setSlug($slugifiedName);
                $newPage->setLink($slugifiedName);
                $newPage->setPosition($page["position"]);
                $newPage->setSubarea($linkedSubArea);
                $newPage->setAuthor($author);
                $this->om->persist($newPage);

                $output->writeln($page["name"] . ' Created');
            } elseif (!$existingPage->isEnabled()){
                $existingPage->enable();
                $this->om->persist($existingPage);
                $output->writeln($page["name"] . ' Enabled');
            } else {
                $output->writeln( $page["name"] . ' already exists !');
            }
        }

        /** flush last items, detach all for doctrine and finish progress */
        $this->om->flush();
        $this->om->clear();
    }
}
