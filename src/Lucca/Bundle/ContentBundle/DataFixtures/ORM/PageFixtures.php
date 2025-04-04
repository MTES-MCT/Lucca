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

use Lucca\Bundle\UserBundle\DataFixtures\ORM\UserFixtures;
use Lucca\Bundle\ContentBundle\Entity\{Page, SubArea};
use Lucca\Bundle\CoreBundle\Utils\Canonalizer;
use Lucca\Bundle\UserBundle\Entity\User;

class PageFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly Canonalizer $canonalizer,
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $pages = [
            ['position' => '1', 'name' => 'Définition', 'subarea' => 'subarea-1', 'author' => 'user-terence', 'ref' => 'page-1'],
            ['position' => '2', 'name' => 'Enjeux', 'subarea' => 'subarea-1', 'author' => 'user-terence', 'ref' => 'page-2'],
            ['position' => '3', 'name' => 'Cartographie', 'subarea' => 'subarea-1', 'author' => 'user-terence', 'ref' => 'page-3'],
            ['position' => '4', 'name' => 'Législation', 'subarea' => 'subarea-1', 'author' => 'user-terence', 'ref' => 'page-4'],
            ['position' => '5', 'name' => 'Signaler un cas', 'subarea' => 'subarea-1', 'author' => 'user-terence', 'ref' => 'page-5'],

            ['position' => '1', 'name' => 'Charte 2015', 'subarea' => 'subarea-2', 'author' => 'user-terence', 'ref' => 'page-6'],
            ['position' => '2', 'name' => 'Cartographie des communes', 'subarea' => 'subarea-2', 'author' => 'user-terence', 'ref' => 'page-7'],
            ['position' => '3', 'name' => 'Comité de pilotage', 'subarea' => 'subarea-2', 'author' => 'user-terence', 'ref' => 'page-8'],
            ['position' => '4', 'name' => 'Animation de la charte', 'subarea' => 'subarea-2', 'author' => 'user-terence', 'ref' => 'page-9'],
            ['position' => '5', 'name' => 'Actions', 'subarea' => 'subarea-2', 'author' => 'user-terence', 'ref' => 'page-27'],
            ['position' => '6', 'name' => 'Guide des élus', 'subarea' => 'subarea-2', 'author' => 'user-terence', 'ref' => 'page-31'],
            ['position' => '7', 'name' => 'Application Lucca', 'subarea' => 'subarea-2', 'author' => 'user-terence', 'ref' => 'page-32'],
            ['position' => '8', 'name' => 'Mobilisation en Occitanie', 'subarea' => 'subarea-2', 'author' => 'user-terence', 'ref' => 'page-33'],

            ['position' => '1', 'name' => 'Articles de presse', 'subarea' => 'subarea-3', 'author' => 'user-terence', 'ref' => 'page-10'],
            ['position' => '2', 'name' => 'Calendrier réunion', 'subarea' => 'subarea-3', 'author' => 'user-terence', 'ref' => 'page-11'],
            ['position' => '3', 'name' => 'Décisions de condamnation', 'subarea' => 'subarea-3', 'author' => 'user-terence', 'ref' => 'page-12'],
            ['position' => '4', 'name' => 'Executions d office', 'subarea' => 'subarea-3', 'author' => 'user-terence', 'ref' => 'page-28'],

            ['position' => '1', 'name' => 'Organisation juridictionnelle ', 'subarea' => 'subarea-7', 'author' => 'user-terence', 'ref' => 'page-19'],
            ['position' => '2', 'name' => 'Déroulé de procédure', 'subarea' => 'subarea-7', 'author' => 'user-terence', 'ref' => 'page-20'],
            ['position' => '3', 'name' => 'Infraction à l urbanisme ', 'subarea' => 'subarea-7', 'author' => 'user-terence', 'ref' => 'page-21'],
            ['position' => '4', 'name' => 'Prescriptions et délits', 'subarea' => 'subarea-7', 'author' => 'user-terence', 'ref' => 'page-22'],
            ['position' => '5', 'name' => 'Actions pénales', 'subarea' => 'subarea-7', 'author' => 'user-terence', 'ref' => 'page-23'],
            ['position' => '5', 'name' => 'Actions civiles', 'subarea' => 'subarea-7', 'author' => 'user-terence', 'ref' => 'page-34'],

            ['position' => '1', 'name' => 'FAQ', 'subarea' => 'subarea-4', 'author' => 'user-terence', 'ref' => 'page-13'],
            ['position' => '2', 'name' => 'Glossaire', 'subarea' => 'subarea-4', 'author' => 'user-terence', 'ref' => 'page-14'],
            ['position' => '3', 'name' => 'Contact', 'subarea' => 'subarea-4', 'author' => 'user-terence', 'ref' => 'page-15'],

            ['position' => '1', 'name' => 'Mentions légales ', 'subarea' => 'subarea-5', 'author' => 'user-terence', 'ref' => 'page-17'],
            ['position' => '2', 'name' => 'Conditions d utilisation ', 'subarea' => 'subarea-5', 'author' => 'user-terence', 'ref' => 'page-18'],
            ['position' => '3', 'name' => 'Crédits ', 'subarea' => 'subarea-5', 'author' => 'user-terence', 'ref' => 'page-24'],

            ['position' => '1', 'name' => 'Adhérent ', 'subarea' => 'subarea-8', 'author' => 'user-terence', 'ref' => 'page-35'],
            ['position' => '2', 'name' => 'Adhérent ', 'subarea' => 'subarea-8', 'author' => 'user-terence', 'ref' => 'page-36'],
            ['position' => '3', 'name' => 'Adhérent ', 'subarea' => 'subarea-8', 'author' => 'user-terence', 'ref' => 'page-37'],
            ['position' => '4', 'name' => 'Adhérent ', 'subarea' => 'subarea-8', 'author' => 'user-terence', 'ref' => 'page-38'],
            ['position' => '5', 'name' => 'Adhérent ', 'subarea' => 'subarea-8', 'author' => 'user-terence', 'ref' => 'page-39'],

            ['position' => '1', 'name' => 'Documentation ', 'subarea' => 'subarea-9', 'author' => 'user-terence', 'ref' => 'page-40'],
            ['position' => '2', 'name' => 'Documentation ', 'subarea' => 'subarea-9', 'author' => 'user-terence', 'ref' => 'page-41'],
            ['position' => '3', 'name' => 'Documentation ', 'subarea' => 'subarea-9', 'author' => 'user-terence', 'ref' => 'page-42'],
            ['position' => '4', 'name' => 'Documentation ', 'subarea' => 'subarea-9', 'author' => 'user-terence', 'ref' => 'page-43'],

            ['position' => '1', 'name' => 'Ouvrir un dossier', 'subarea' => 'subarea-10', 'author' => 'user-terence', 'ref' => 'page-50'],
            ['position' => '2', 'name' => 'Déclarer un contrôle', 'subarea' => 'subarea-10', 'author' => 'user-terence', 'ref' => 'page-51'],
            ['position' => '3', 'name' => 'Déclarer un PV', 'subarea' => 'subarea-10', 'author' => 'user-terence', 'ref' => 'page-52'],
            ['position' => '4', 'name' => 'Envoyer les courriers', 'subarea' => 'subarea-10', 'author' => 'user-terence', 'ref' => 'page-53'],
            ['position' => '5', 'name' => 'Actualiser un dossier', 'subarea' => 'subarea-10', 'author' => 'user-terence', 'ref' => 'page-54'],
            ['position' => '6', 'name' => 'Les décisions de justice', 'subarea' => 'subarea-10', 'author' => 'user-terence', 'ref' => 'page-55'],
            ['position' => '7', 'name' => 'Cloturer un dossier', 'subarea' => 'subarea-10', 'author' => 'user-terence', 'ref' => 'page-56'],
        ];

        $icon = "fa fa-angle-right";
        $content = "Cras ultricies ligula sed magna dictum porta. Sed porttitor lectus nibh. Vivamus magna justo, lacinia eget consectetur sed, convallis at tellus. Nulla porttitor accumsan tincidunt. Nulla porttitor accumsan tincidunt.<br><br>";

        foreach ($pages as $page) {

            $newPage = new Page();
            $newPage->setName($page['name']);
            $newPage->setSubarea($this->getReference($page['subarea'], SubArea::class));
            $newPage->setSlug($this->canonalizer->slugify($newPage->getName()));
            $newPage->setIcon($icon);
            $newPage->setLink($page['name']);
            $newPage->setPosition($page['position']);
            $newPage->setContent($content . $content . $content . $content);
            $newPage->setAuthor($this->getReference($page['author'], User::class));

            $manager->persist($newPage);

            $this->addReference($page['ref'], $newPage);
        }

        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            SubAreaFixtures::class,
        ];
    }
}
