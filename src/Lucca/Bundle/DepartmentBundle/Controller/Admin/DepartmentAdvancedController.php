<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * Affero General Public License (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\DepartmentBundle\Controller\Admin;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Lucca\Bundle\SettingBundle\Generator\SettingGenerator;

/**
 * Class DepartmentController
 *
 * @package Lucca\Bundle\DepartmentBundle\Controller\Admin
 */
#[Route(path: '/department')]
#[IsGranted("ROLE_SUPER_ADMIN")]
class DepartmentAdvancedController extends AbstractController
{

    /**
     * DepartmentController constructor.
     */
    public function __construct(
        private readonly SettingGenerator  $settingGenerator,
    )
    {
    }

    /**
     * Add missing settings.
     */
    #[Route(path: '-{id}/generateSettings', name: 'lucca_department_admin_generate_settings', defaults: ['_locale' => 'fr'], methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function generateSettingsAction(Department $department, ManagerRegistry $doctrine): Response
    {
        try {
            $this->settingGenerator->generateMissingSettings($department);
            $this->addFlash('success', 'flash.settings.updatedSuccessfully');
            $department->setLastSyncSetting(new \DateTime());
            $em = $doctrine->getManager();
            $em->persist($department);
            $em->flush();
        }catch (\Exception $e){
            $this->addFlash('danger', 'flash.settings.updatedError');
        }

        return $this->redirectToRoute('lucca_department_admin_show', ['id' => $department->getId()]);
    }
}
