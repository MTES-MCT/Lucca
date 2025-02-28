<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\MinuteBundle\Controller\Admin;

use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute;
use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Plot;
use Doctrine\ORM\ORMException;
use Lucca\MinuteBundle\Form\MinuteBrowserType;
use Lucca\MinuteBundle\Form\MinuteType;
use Lucca\ParameterBundle\Entity\Intercommunal;
use Lucca\ParameterBundle\Entity\Town;
use Lucca\SettingBundle\Utils\SettingManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MinuteController
 *
 * @Route("/minute")
 * @Security("has_role('ROLE_USER')")
 *
 * @package Lucca\MinuteBundle\Controller\Admin
 * @author Terence <terence@numeric-wave.tech>
 */
class MinuteController extends Controller
{

    /**
     * Filter by rolling year
     * @var bool
     */
    private $filterByRollingYear;

    /**
     * Add current adherent to filter
     * @var bool
     */
    private $presetAdherentByConnectedUser;

    /**
     * MinuteController constructor.
     */
    public function __construct()
    {
        $this->filterByRollingYear = SettingManager::get('setting.folder.indexFilterByRollingOrCalendarYear.name');
        $this->presetAdherentByConnectedUser = SettingManager::get('setting.folder.presetFilterAdherentByConnectedUser.name');
    }

    /**
     * List of Minute
     *
     * @Route("/", name="lucca_minute_index", methods={"GET", "POST"})
     * @Security("has_role('ROLE_USER')")
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** Who is connected ;) */
        $adherent = $this->get('lucca.finder.adherent')->whoAmI();

        $adherentTowns = null;
        $adherentIntercommunals = null;

        /** @var array $filters - Init default filters */
        $filters = array();

        /** if is not admin get all town and intercommunal form adherent*/
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            //  If the adherent is link to a service it's mean he can see all the town
            if ($adherent->getService()) {
                $adherentTowns = $em->getRepository(Town::class)->findAll();
                $adherentIntercommunals = $em->getRepository(Intercommunal::class)->findAll();
            } else {
                $adherentTowns = $this->get('lucca.utils.parameter')->getTownByAdherents(array($adherent));
                $adherentIntercommunals = $adherent->getIntercommunal() ? array($adherent->getIntercommunal()) : array();
            }
        }

        $form = $this->createForm(MinuteBrowserType::class, null, array(
            'adherent_towns' => $adherentTowns,
            'adherent_intercommunals' => $adherentIntercommunals,
            'allFiltersAvailable' => $adherent->getService() !== null
        ));

        /** Init filters */
        $filters['dateStart'] = new \DateTime($this->filterByRollingYear ? 'last day of this month - 1 year' : 'first day of January');
        $filters['dateEnd'] = new \DateTime($this->filterByRollingYear ? 'last day of this month ' : 'last day of December');
        $filters['num'] = null;
        $filters['status'] = null;
        $filters['adherent_intercommunal'] = null;
        $filters['adherent_town'] = null;


        if ($adherentTowns !== null && count($adherentTowns) === 1)
            $filters['folder_town'] = $adherentTowns;
        else
            $filters['folder_town'] = null;

        if ($adherentIntercommunals !== null && count($adherentIntercommunals) === 1)
            $filters['folder_intercommunal'] = $adherentIntercommunals;
        else
            $filters['folder_intercommunal'] = null;

        //  If the adherent is link to a service it's mean he can see all filters
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') || $adherent->getService()) {
            $filters['adherent'] = $this->presetAdherentByConnectedUser ? array($adherent) : null;
        } else {
            $filters['adherent'] = array($adherent);
        }
        $filters['service'] = null;

        /** If dateStart is not filled - init it */
        if ($form->get('dateStart')->getData() === null)
            $form->get('dateStart')->setData($filters['dateStart']);
        /** If dateEnd is not filled - init it */
        if ($form->get('dateEnd')->getData() === null)
            $form->get('dateEnd')->setData($filters['dateEnd']);

        if ($form->has('adherent')) {
            /** If adherent is not filled - init it */
            $form->get('adherent')->setData($filters['adherent']);
        }


        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            /** Configure filters with form data */
            $filters['dateStart'] = new \DateTime($form->get('dateStart')->getData()->format('Y-m-d') . ' 00:00');
            $filters['dateEnd'] = new \DateTime($form->get('dateEnd')->getData()->format('Y-m-d') . ' 23:59');

            $filters['num'] = $form->get('num')->getData();
            // For the moment, all minutes don't have a status
            $filters['status'] = $form->get('status')->getData();

            if ($form->has('folder_town'))
                $filters['folder_town'] = $form->get('folder_town')->getData();
            if ($form->has('folder_intercommunal'))
                $filters['folder_intercommunal'] = $form->get('folder_intercommunal')->getData();

            if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') || $adherent->getService()) {
                $filters['adherent'] = $form->get('adherent')->getData();
                $filters['service'] = $form->get('service')->getData();
                $filters['adherent_intercommunal'] = $form->get('adherent_intercommunal')->getData();
                $filters['adherent_town'] = $form->get('adherent_town')->getData();
            }

            // check filters interval date
            if (!$this->get('lucca.manager.minute')->checkFilters($filters)) {
                $this->addFlash('danger', 'flash.minute.filters_too_large');
                /** Init default filters */
                $filters['dateStart'] = new \DateTime($this->filterByRollingYear ? 'last day of this month - 1 year' : 'first day of January');
                $filters['dateEnd'] = new \DateTime($this->filterByRollingYear ? 'last day of this month ' : 'last day of December');
//                why redirect to route keep form values ??????
//                $this->redirectToRoute('lucca_minute_index');
            }
        }

        /** Get minutes in Repo with filters */
        //  If the adherent is link to a service it's mean he can see all the data
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') || $adherent->getService())
            $minutes = $em->getRepository('LuccaMinuteBundle:Minute')->findMinutesBrowser(null,
                $filters['dateStart'],
                $filters['dateEnd'],
                $filters['num'], $filters['status'], $filters['adherent'], $filters['folder_town'], $filters['folder_intercommunal'], $filters['service'], $filters['adherent_town'], $filters['adherent_intercommunal']);
        else
            $minutes = $em->getRepository('LuccaMinuteBundle:Minute')->findMinutesBrowser($adherent,
                $filters['dateStart'], $filters['dateEnd'], $filters['num'], $filters['status'], null, $filters['folder_town'], $filters['folder_intercommunal']);

        return $this->render('@LuccaMinute/Minute/browser.html.twig', array(
            'form' => $form->createView(),
            'filters' => $filters,
            'adherent' => $adherent,
            'minutes' => $minutes
        ));
    }

    /**
     * Creates a new Minute entity.
     *
     * @Route("/new", name="lucca_minute_new", methods={"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws ORMException
     */
    public function newAction(Request $request)
    {
        $minute = new Minute();

        /** Who is connected ;) */
        $adherent = $this->get('lucca.finder.adherent')->whoAmI();
        if ($adherent)
            $minute->setAdherent($adherent);

        /** Security test - User cannot create Minute if he haven't an Agent Entity created before */
        if (sizeof($adherent->getAgents()) === 0) {
            $this->addFlash('info', 'flash.minute.create_agent_before_minute');
            return $this->redirectToRoute('lucca_myagent_new');
        }

        $em = $this->getDoctrine()->getManager();

        /** If the this action is call by right click on map */
        if (!empty($_SESSION)
            && array_key_exists("addrRoute", $_SESSION) && array_key_exists("addrCode", $_SESSION) && array_key_exists("addrCity", $_SESSION)) {
            $minute->setPlot(new Plot());
            $minute->getPlot()->setAddress($_SESSION["addrRoute"]);
            /** @var Town $town */
            $town = $em->getRepository('LuccaParameterBundle:Town')->findOneBy(['code' => $_SESSION["addrCode"]]);
            if (!$town)
                $town = $em->getRepository('LuccaParameterBundle:Town')->findOneBy(['name' => strtoupper($_SESSION["addrCity"])]);
            $minute->getPlot()->setTown($town);

            /** Clear address data from session */
            unset($_SESSION["addrRoute"]);
            unset($_SESSION["addrCode"]);
            unset($_SESSION["addrCity"]);
        }

        $form = $this->createForm(MinuteType::class, $minute, array(
            'adherent' => $adherent
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() &&
            $this->get('lucca.manager.minute')->checkMinute($minute)) {

            $minute->setNum($this->get('lucca.generator.minute_num')->generate($minute));

            if ($minute->getDateComplaint())
                $minute->setDateOpening($minute->getDateComplaint());

            /** Call geo locator service to set latitude and longitude of plot */
            $plot = $minute->getPlot();
            $this->get('lucca.manager.plot')->manageLocation($plot);

            if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') &&
                $adherent->getTown() && $adherent->getTown() !== $plot->getTown()) {
                $this->addFlash('warning', 'flash.plot.townNotAuthorize');
            } else {
                $em->persist($minute);
                $em->flush();

                /** update status of the minute */
                $this->get('lucca.manager.minute_story')->manage($minute);
                $em->flush();

                $this->addFlash('success', 'flash.minute.createdSuccessfully');
                return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
            }
        }

        return $this->render('LuccaMinuteBundle:Minute:new.html.twig', array(
            'minute' => $minute,
            'adherent' => $adherent,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Minute entity.
     *
     * @Route("-{id}", name="lucca_minute_show", methods={"GET"})
     * @Security("has_role('ROLE_VISU')")
     *
     * @param Minute $minute
     * @return Response|null
     * @throws ORMException
     */
    public function showAction(Minute $minute)
    {
        /** Who is connected ;) */
        $adherent = $this->get('lucca.finder.adherent')->whoAmI();

        /** Check if the adherent can access to the minute */
        if (!$this->get('lucca.manager.minute')->checkAccessMinute($minute, $adherent)) {
            $this->addFlash('warning', 'flash.minute.cantAccess');
            return $this->redirectToRoute('lucca_minute_index');
        }

        $em = $this->getDoctrine()->getManager();

        $session = $this->get('session');
        $session->set('refresh', false);

        $deleteForm = $this->createDeleteForm($minute);

        /** Get Decision value */
        $decisions = $em->getRepository('LuccaMinuteBundle:Decision')->findDecisionsByMinute($minute);

        /** Verify first if status exist */
        if ($minute->getStatus() === null) {
            $this->get('lucca.manager.minute')->updateStatusAction($minute);
            $this->get('lucca.manager.minute_story')->manage($minute);
            $em->flush();
        }
        /** Get Minute Story to get status of the minute */
        $minuteStory = $em->getRepository('LuccaMinuteBundle:MinuteStory')->findLastByMinute($minute)[0];

        return $this->render('LuccaMinuteBundle:Minute:show.html.twig', array(
            'minute' => $minute,
            'minuteStory' => $minuteStory,
            'decisions' => $decisions,
            'closure' => $minute->getClosure(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Minute entity.
     *
     * @Route("-{id}/edit", name="lucca_minute_edit", methods={"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Request $request
     * @param Minute $minute
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Minute $minute)
    {
        /** Who is connected ;) */
        $adherent = $this->get('lucca.finder.adherent')->whoAmI();

        /** Check if the adherent can access to the minute */
        if (!$this->get('lucca.manager.minute')->checkAccessMinute($minute, $adherent)) {
            $this->addFlash('warning', 'flash.minute.cantAccess');
            return $this->redirectToRoute('lucca_minute_index');
        }

        $editForm = $this->createForm(MinuteType::class, $minute, array(
            'adherent' => $adherent,
        ));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid() &&
            $this->get('lucca.manager.minute')->checkMinute($minute)) {
            $em = $this->getDoctrine()->getManager();

            /** Call geo locator service to set latitude and longitude of plot */
            $plot = $minute->getPlot();
            $this->get('lucca.manager.plot')->manageLocation($plot);

            if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') &&
                $adherent->getTown() && $adherent->getTown() !== $plot->getTown()) {
                $this->addFlash('warning', 'flash.plot.townNotAuthorize');
            } else {
                $em->persist($minute);
                $em->flush();

                $this->addFlash('info', 'flash.minute.updatedSuccessfully');
                return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
            }
        }

        return $this->render('LuccaMinuteBundle:Minute:edit.html.twig', array(
            'minute' => $minute,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Minute entity.
     *
     * @Route("-{id}", name="lucca_minute_delete", methods={"DELETE"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Request $request
     * @param Minute $minute
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Minute $minute)
    {
        $form = $this->createDeleteForm($minute);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($minute);
            $em->flush();
            $this->addFlash('danger', 'flash.minute.deletedSuccessfully');
        }

        return $this->redirectToRoute('lucca_minute_index');
    }

    /**
     * Creates a form to delete a Minute entity.
     *
     * @param Minute $minute
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Minute $minute)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_minute_delete', array('id' => $minute->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
