<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Controller\Dashboard;

use Lucca\Bundle\SettingBundle\Utils\SettingManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Security,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Routing\Annotation\Route;

use Lucca\Bundle\FolderBundle\Form\Folder\FolderBrowserType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Class FolderController
 *
 * @package Lucca\Bundle\FolderBundle\Controller\Dashboard
 * @author Terence <terence@numeric-wave.tech>
 */
#[Route('/folder')]
#[IsGranted('ROLE_USER')]
class FolderController extends AbstractController
{

    /**
     * Filter by rolling year
     * @var bool
     */
    private $filterByRollingYear;

    /**
     * MinuteController constructor.
     */
    public function __construct()
    {
        $this->filterByRollingYear = SettingManager::get('setting.folder.indexFilterByRollingOrCalendarYear.name');
    }

    /**
     * List of Folder
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    #[Route('/', name: 'lucca_folder_dashboard', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** Who is connected ;) */
        $adherent = $this->get('lucca.finder.adherent')->whoAmI();

        /** @var array $filters - Init default filters */
        $filters = array();

        $form = $this->createForm(FolderBrowserType::class);

        /** Init Filters */
        $filters['dateStart'] = new \DateTime($this->filterByRollingYear ? 'last day of this month - 1 year' : 'first day of January');
        $filters['dateEnd'] = new \DateTime($this->filterByRollingYear ? 'last day of this month ' : 'last day of December');
        $filters['num'] = null;
        $filters['numFolder'] = null;
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $filters['adherent'] = null;
            $filters['town'] = null;
            $filters['intercommunal'] = null;
            $filters['service'] = null;
        }

        /** If dateStart is not filled - init it */
        if ($form->get('dateStart')->getData() === null)
            $form->get('dateStart')->setData($filters['dateStart']);
        /** If dateEnd is not filled - init it */
        if ($form->get('dateEnd')->getData() === null)
            $form->get('dateEnd')->setData($filters['dateEnd']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** Configure filters with form data */
            $filters['dateStart'] = new \DateTime($form->get('dateStart')->getData()->format('Y-m-d') . ' 00:00');
            $filters['dateEnd'] = new \DateTime($form->get('dateEnd')->getData()->format('Y-m-d') . ' 23:59');

            $filters['num'] = $form->get('num')->getData();
            $filters['numFolder'] = $form->get('numFolder')->getData();
            if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                $filters['adherent'] = $form->get('adherent')->getData();
                $filters['town'] = $form->get('town')->getData();
                $filters['intercommunal'] = $form->get('intercommunal')->getData();
                $filters['service'] = $form->get('service')->getData();
            }
        }

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
            $folders = $em->getRepository('LuccaFolderBundle:Folder')->findFolderBrowser(null,
                $filters['dateStart'], $filters['dateEnd'], $filters['num'], $filters['numFolder'], $filters['adherent'], $filters['town'], $filters['intercommunal'], $filters['service']);
        else
            $folders = $em->getRepository('LuccaFolderBundle:Folder')->findFolderBrowser($adherent,
                $filters['dateStart'], $filters['dateEnd'], $filters['num'], $filters['numFolder']);

        return $this->render('@LuccaFolder/Dashboard/folder.html.twig', array(
            'form' => $form->createView(),
            'filters' => $filters,
            'adherent' => $adherent,
            'folders' => $folders
        ));
    }


}