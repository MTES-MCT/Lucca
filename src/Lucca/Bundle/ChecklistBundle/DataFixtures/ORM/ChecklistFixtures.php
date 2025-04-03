<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ChecklistBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

use Lucca\Bundle\ChecklistBundle\Entity\Checklist;

class ChecklistFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $checkLists = [
            ['name' => 'Liste PV', 'status' => 'choice.status.minute', 'ref' => 'checklist-1'],
            ['name' => 'Liste Actualisation', 'status' => 'choice.status.updating', 'ref' => 'checklist-2'],
        ];

        foreach ($checkLists as $checklist) {

            $newChecklist = new Checklist();
            $newChecklist->setName($checklist['name']);
            $newChecklist->setStatus($checklist['status']);
            $newChecklist->setEnabled(true);

            $manager->persist($newChecklist);
            $this->addReference($checklist['ref'], $newChecklist);
        }

        $manager->flush();
    }
}
