<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ContentBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

use Lucca\Bundle\ContentBundle\Entity\Area;

class AreaFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $areas = [
            ['name' => 'Zone principale', 'position' => 'choice.position.content', 'ref' => 'area-main'],
            ['name' => 'Pied de page', 'position' => 'choice.position.footer', 'ref' => 'area-footer'],
            ['name' => 'Administration', 'position' => 'choice.position.admin', 'ref' => 'area-admin'],
        ];

        foreach ($areas as $area) {

            $newArea = new Area();

            $newArea->setName($area['name']);
            $newArea->setPosition($area['position']);

            $manager->persist($newArea);
            $this->addReference($area['ref'], $newArea);
        }

        $manager->flush();
    }
}
