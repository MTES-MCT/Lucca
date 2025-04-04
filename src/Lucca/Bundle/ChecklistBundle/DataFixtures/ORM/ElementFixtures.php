<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ChecklistBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\{AbstractFixture, DependentFixtureInterface};
use Doctrine\Persistence\ObjectManager;

use Lucca\Bundle\ChecklistBundle\Entity\{Element, Checklist};

class ElementFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $elements = [
            ['name' => 'Relevé de propriété', 'position' => 1, 'checklist' => 'checklist-1', 'ref' => 'element-1'],
            ['name' => 'Extrait de plan cadastral', 'position' => 2, 'checklist' => 'checklist-1', 'ref' => 'element-2'],
            ['name' => 'Plan de situation', 'position' => 3, 'checklist' => 'checklist-1', 'ref' => 'element-3'],
            ['name' => 'Planche photographique', 'position' => 4, 'checklist' => 'checklist-1', 'ref' => 'element-4'],
            ['name' => 'Extrait du règlement et du plan de la zone concernée du document d’urbanisme', 'position' => 5, 'checklist' => 'checklist-1', 'ref' => 'element-5'],
            ['name' => 'Extrait du règlement et du plan de la zone concernée règlement du Plan de Prévention des Risques', 'position' => 6, 'checklist' => 'checklist-1', 'ref' => 'element-6'],
            ['name' => 'Plan de localisation des installations/constructions (plan schématique et/ou photo aérienne)', 'position' => 7, 'checklist' => 'checklist-1', 'ref' => 'element-7'],
            ['name' => 'Autorisation d’accès à la propriété', 'position' => 8, 'checklist' => 'checklist-1', 'ref' => 'element-8'],
            ['name' => 'Carte de commissionnement de l\'agent verbalisateur', 'position' => 9, 'checklist' => 'checklist-1', 'ref' => 'element-9'],
            ['name' => 'Courrier à l\'intéressé informant de la visite', 'position' => 10, 'checklist' => 'checklist-1', 'ref' => 'element-10'],
            ['name' => 'Courrier de mise en demeure à l\'intéressé après clôture du PV', 'position' => 11, 'checklist' => 'checklist-1', 'ref' => 'element-11'],
            ['name' => 'Refus d’accès à la propriété', 'position' => 12, 'checklist' => 'checklist-1', 'ref' => 'element-12'],

            ['name' => 'Relevé de propriété', 'position' => 1, 'checklist' => 'checklist-2', 'ref' => 'element-21'],
            ['name' => 'Extrait de plan cadastral', 'position' => 2, 'checklist' => 'checklist-2', 'ref' => 'element-22'],
            ['name' => 'Plan de situation', 'position' => 3, 'checklist' => 'checklist-2', 'ref' => 'element-23'],
            ['name' => 'Planche photographique', 'position' => 4, 'checklist' => 'checklist-2', 'ref' => 'element-24'],
            ['name' => 'Extrait du règlement et du plan de la zone concernée du document d’urbanisme', 'position' => 5, 'checklist' => 'checklist-2', 'ref' => 'element-25'],
            ['name' => 'Extrait du règlement et du plan de la zone concernée règlement du Plan de Prévention des Risques', 'position' => 6, 'checklist' => 'checklist-2', 'ref' => 'element-26'],
            ['name' => 'Plan de localisation des installations/constructions (plan schématique et/ou photo aérienne)', 'position' => 7, 'checklist' => 'checklist-2', 'ref' => 'element-27'],
            ['name' => 'Autorisation d’accès à la propriété', 'position' => 8, 'checklist' => 'checklist-2', 'ref' => 'element-28'],
            ['name' => 'Carte de commissionnement de l\'agent verbalisateur', 'position' => 9, 'checklist' => 'checklist-2', 'ref' => 'element-29'],
            ['name' => 'Courrier à l\'intéressé informant de la visite', 'position' => 10, 'checklist' => 'checklist-2', 'ref' => 'element-30'],
            ['name' => 'Courrier de mise en demeure à l\'intéressé après clôture du PV', 'position' => 11, 'checklist' => 'checklist-2', 'ref' => 'element-31'],
            ['name' => 'Refus d’accès à la propriété', 'position' => 12, 'checklist' => 'checklist-2', 'ref' => 'element-32'],
        ];

        foreach ($elements as $element) {

            $newElement = new Element();
            $newElement->setName($element['name']);
            $newElement->setPosition($element['position']);
            $newElement->setEnabled(true);

            $newElement->setChecklist($this->getReference($element['checklist'], Checklist::class));

            $manager->persist($newElement);
            $this->addReference($element['ref'], $newElement);
        }

        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getDependencies(): array
    {
        return [
            ChecklistFixtures::class,
        ];
    }
}
