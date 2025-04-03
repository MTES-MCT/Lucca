<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ContentBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\{AbstractFixture, DependentFixtureInterface};
use Doctrine\Persistence\ObjectManager;

use Lucca\Bundle\ContentBundle\Entity\{Area, SubArea};

class SubAreaFixtures extends AbstractFixture  implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $subAreas = [
            ['name' => 'Comprendre la cabanisation', 'position' => '1', 'color' => '#f05050', 'area' => 'area-main', 'ref' => 'subarea-1'],
            ['name' => 'Mobilisation et actions ', 'position' => '2', 'color' => '#826969', 'area' => 'area-main', 'ref' => 'subarea-2'],
            ['name' => 'Les Actualités ', 'position' => '3', 'color' => '#7025cf', 'area' => 'area-main', 'ref' => 'subarea-3'],
            ['name' => 'Dispositions pénales ', 'position' => '4', 'color' => '#28d2eb', 'area' => 'area-main', 'ref' => 'subarea-7'],

            ['name' => 'Aide', 'position' => '1', 'color' => '#ecff29', 'area' => 'area-footer', 'ref' => 'subarea-4'],
            ['name' => 'Légal', 'position' => '2', 'color' => '#ff9a3d', 'area' => 'area-footer', 'ref' => 'subarea-5'],

            ['name' => 'Utilisateur', 'position' => '1', 'color' => '#5d9cec', 'area' => 'area-admin', 'ref' => 'subarea-8'],
            ['name' => 'Documentation', 'position' => '2', 'color' => '#ff902b', 'area' => 'area-admin', 'ref' => 'subarea-9'],
            ['name' => 'Utilisation de Lucca', 'position' => '3', 'color' => '#4EBEE6', 'area' => 'area-admin', 'ref' => 'subarea-10'],
        ];

        $width_main = "col-lg-3 col-md-4 col-sm-6 col-xs-12";
        $width_footer = "col-lg-4 col-md-4 col-sm-6 col-xs-12";

        foreach ($subAreas as $subArea) {

            $newSubArea = new SubArea();

            $newSubArea->setName($subArea['name']);
            $newSubArea->setPosition($subArea['position']);
            $newSubArea->setArea($this->getReference($subArea['area'], Area::class));

            if ($subArea['area'] == 'area-main') {
                $newSubArea->setWidth($width_main);
            } else {
                $newSubArea->setWidth($width_footer);
            }

            $newSubArea->setColor($subArea['color']);
            $newSubArea->setTitle($subArea['name']);

            $manager->persist($newSubArea);
            $this->addReference($subArea['ref'], $newSubArea);
        }

        $manager->flush();
    }


    /**
     * @inheritdoc
     */
    public function getDependencies(): array
    {
        return [
            AreaFixtures::class,
        ];
    }
}
