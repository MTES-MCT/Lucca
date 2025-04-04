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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Lucca\Bundle\DepartmentBundle\Form\DepartmentType;

/**
 * Class DepartmentController
 *
 * @package Lucca\Bundle\DepartmentBundle\Controller\Admin
 */
#[Route(path: '/department')]
#[IsGranted("ROLE_SUPER_ADMIN")]
class DepartmentController extends AbstractController
{

    /**
     * DepartmentController constructor.
     */
    public function __construct(
    )
    {
    }

    /**
     * List of Department
     */
    #[Route(path: '/', name: 'lucca_department_admin_index', methods: ['GET'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function indexAction(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();

        $departments = $em->getRepository(Department::class)->findAll();
        return $this->render('@LuccaDepartment/Admin/Department/browser.html.twig', array(
            'departments' => $departments,
        ));
    }

    /**
     * Creates a new Department entity.
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route(path: '/new', name: 'lucca_department_admin_new', defaults: ['_locale' => 'fr'], methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function newAction(Request $request, ManagerRegistry $doctrine): Response
    {
        $department = new Department();
        $em = $doctrine->getManager();

        $form = $this->createForm(DepartmentType::class, $department);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($department);
            $em->flush();
            $this->addFlash('success', 'flash.department.create.success');
            return $this->redirectToRoute('lucca_department_admin_show', ['id' => $department->getId()]);
        }

        return $this->render('@LuccaDepartment/Admin/Department/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Department entity.
     */
    #[Route(path: '-{id}', name: 'lucca_department_admin_show', defaults: ['_locale' => 'fr'], methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function showAction(Department $department): Response
    {
        $deleteForm = $this->createDeleteForm($department);
        return $this->render('@LuccaDepartment/Admin/Department/show.html.twig', [
            'department' => $department,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Edits an existing Department entity.
     */
    #[Route(path: '-{id}/edit', name: 'lucca_department_admin_edit', defaults: ['_locale' => 'fr'], methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function editAction(Request $request, Department $department, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();

        $form = $this->createForm(DepartmentType::class, $department);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($department);
            $em->flush();
            $this->addFlash('success', 'flash.department.update.success');

            return $this->redirectToRoute('lucca_department_admin_show', ['id' => $department->getId()]);
        }

        return $this->render('@LuccaDepartment/Admin/Department/edit.html.twig', [
            'form' => $form->createView(),
            'department' => $department,
        ]);
    }

    /**
     * Deletes a Department entity.
     */
    #[Route(path: '-{id}', name: 'lucca_department_admin_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function deleteAction(Request $request, ManagerRegistry $doctrine, Department $department): RedirectResponse
    {
        $form = $this->createDeleteForm($department);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->remove($department);
            $em->flush();

            $this->addFlash('success', 'flash.department.deleted.success');
        } else
            $this->addFlash('error', 'flash.department.deleted.error');

        return $this->redirectToRoute('lucca_department_admin_index');
    }

    /**
     * Creates a form to delete a Department entity.
     */
    private function createDeleteForm(Department $department): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_department_admin_delete', array('id' => $department->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Finds and enable / disable a Department entity.
     */
    #[Route(path: '-{id}/disable', name: 'lucca_department_admin_disable', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[Route(path: '-{id}/enable', name: 'lucca_department_admin_enable', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function toggleAction(ManagerRegistry $doctrine, Department $department): RedirectResponse
    {
        $em = $doctrine->getManager();

        if ($department->getEnabled()) {
            $department->setEnabled(false);
            $this->addFlash('success', 'flash.department.disabledSuccessfully');
        } else {
            $department->setEnabled(true);
            $this->addFlash('success', 'flash.department.enabledSuccessfully');
        }

        $em->persist($department);
        $em->flush();

        return $this->redirectToRoute('lucca_department_admin_index');
    }
}
