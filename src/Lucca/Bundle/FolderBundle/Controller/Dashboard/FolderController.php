<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\MinuteBundle\Controller\Dashboard;

use Lucca\SettingBundle\Utils\SettingManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Security,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Routing\Annotation\Route;

use Lucca\MinuteBundle\Form\Folder\FolderBrowserType;

/**
 * Class FolderController
 *
 * @Route("/folder")
 * @Security("has_role('ROLE_USER')")
 *
 * @package Lucca\MinuteBundle\Controller\Dashboard
 * @author Terence <terence@numeric-wave.tech>
 */
class FolderController extends Controller
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
     * @Route("/", name="lucca_folder_dashboard", methods={"GET", "POST"})
     * @Security("has_role('ROLE_USER')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
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
            $folders = $em->getRepository('LuccaMinuteBundle:Folder')->findFolderBrowser(null,
                $filters['dateStart'], $filters['dateEnd'], $filters['num'], $filters['numFolder'], $filters['adherent'], $filters['town'], $filters['intercommunal'], $filters['service']);
        else
            $folders = $em->getRepository('LuccaMinuteBundle:Folder')->findFolderBrowser($adherent,
                $filters['dateStart'], $filters['dateEnd'], $filters['num'], $filters['numFolder']);

        return $this->render('LuccaMinuteBundle:Dashboard:folder.html.twig', array(
            'form' => $form->createView(),
            'filters' => $filters,
            'adherent' => $adherent,
            'folders' => $folders
        ));
    }


}