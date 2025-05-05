<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SettingBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Lucca\Bundle\DepartmentBundle\Service\UserDepartmentResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\Attribute\Route;

use Lucca\Bundle\SettingBundle\Entity\{Category, Setting};
use Lucca\Bundle\SettingBundle\Form\SettingType;
use Lucca\Bundle\SettingBundle\Generator\SettingGenerator;

#[Route(path: '/setting')]
#[IsGranted('ROLE_ADMIN')]
class SettingController extends AbstractController
{
    public function __construct(
        private readonly UserDepartmentResolver        $userDepartmentResolver,
        private readonly EntityManagerInterface        $em,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly SettingGenerator              $generatorSetting,
    )
    {
    }

    /**
     * List of Settings
     */
    #[Route(path: '/', name: 'lucca_setting_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function indexAction(): Response
    {
        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $settings = $this->em->getRepository(Setting::class)->findAllByRole();
        } else {
            $settings = $this->em->getRepository(Setting::class)->findAllByRole(false);
        }
        $categories = $this->em->getRepository(Category::class)->findAll();

        return $this->render('@LuccaSetting/Setting/index.html.twig', [
            'settings' => $settings,
            'categories' => $categories
        ]);
    }

    /**
     * Finds and displays a Setting entity.
     */
    #[Route(path: '/{id}', name: 'lucca_setting_show', options: ['expose' => true], methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showAction(Setting $setting): Response
    {
        if (str_contains($setting->getAccessType(), 'superAdmin') && !$this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $this->addFlash('danger', 'flash.setting.accessDenied');

            return $this->redirectToRoute('lucca_setting_index');
        }

        return $this->render('@LuccaSetting/Setting/show.html.twig', [
            'setting' => $setting
        ]);
    }

    /**
     * Displays a form to edit an existing Setting entity.
     */
    #[Route(path: '/{id}/edit', name: 'lucca_setting_edit', options: ['expose' => true], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editAction(Request $request, Setting $setting): Response
    {
        if (str_contains($setting->getAccessType(), 'superAdmin') && !$this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $this->addFlash('danger', 'flash.setting.accessDenied');

            return $this->redirectToRoute('lucca_setting_index');
        }

        $editForm = $this->createForm(SettingType::class, $setting);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->em->persist($setting);
            $this->em->flush();

            $department = $this->userDepartmentResolver->getDepartment();

            $this->generatorSetting->updateCachedSetting($setting->getName(), $setting->getValue(), $department);

            $this->addFlash('success', 'flash.setting.updatedSuccessfully');

            return $this->redirectToRoute('lucca_setting_show', ['id' => $setting->getId()]);
        }

        return $this->render('@LuccaSetting/Setting/edit.html.twig', [
            'setting' => $setting,
            'edit_form' => $editForm->createView(),
        ]);
    }
}
