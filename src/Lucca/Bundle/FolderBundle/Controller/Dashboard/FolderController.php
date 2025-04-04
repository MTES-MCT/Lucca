<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * Affero General Public License (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Controller\Dashboard;

use DateTime;
use Exception;
use Doctrine\ORM\EntityManagerInterface;
use Lucca\Bundle\FolderBundle\Entity\Folder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\AdherentBundle\Finder\AdherentFinder;
use Lucca\Bundle\FolderBundle\Form\Folder\FolderBrowserType;
use Lucca\Bundle\SettingBundle\Manager\SettingManager;

#[Route(path: '/folder')]
#[IsGranted('ROLE_USER')]
class FolderController extends AbstractController
{
    /**
     * Filter by rolling year
     */
    private bool $filterByRollingYear;

    /**
     * MinuteController constructor.
     */
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly AdherentFinder $adherentFinder,
    )
    {
        $this->filterByRollingYear = SettingManager::get('setting.folder.indexFilterByRollingOrCalendarYear.name');
    }

    /**
     * List of Folder
     *
     * @throws Exception
     */
    #[Route(path: '/', name: 'lucca_folder_dashboard', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function indexAction(Request $request): Response
    {
        /** Who is connected  */
        $adherent = $this->adherentFinder->whoAmI();

        /** Init default filters */
        $filters = array();

        $form = $this->createForm(FolderBrowserType::class);

        /** Init Filters */
        $filters['dateStart'] = new DateTime($this->filterByRollingYear ? 'last day of this month - 1 year' : 'first day of January');
        $filters['dateEnd'] = new DateTime($this->filterByRollingYear ? 'last day of this month ' : 'last day of December');
        $filters['num'] = null;
        $filters['numFolder'] = null;
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $filters['adherent'] = null;
            $filters['town'] = null;
            $filters['intercommunal'] = null;
            $filters['service'] = null;
        }

        /** If dateStart is not filled - init it */
        if ($form->get('dateStart')->getData() === null) {
            $form->get('dateStart')->setData($filters['dateStart']);
        }

        /** If dateEnd is not filled - init it */
        if ($form->get('dateEnd')->getData() === null) {
            $form->get('dateEnd')->setData($filters['dateEnd']);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** Configure filters with form data */
            $filters['dateStart'] = new DateTime($form->get('dateStart')->getData()->format('Y-m-d') . ' 00:00');
            $filters['dateEnd'] = new DateTime($form->get('dateEnd')->getData()->format('Y-m-d') . ' 23:59');

            $filters['num'] = $form->get('num')->getData();
            $filters['numFolder'] = $form->get('numFolder')->getData();
            if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                $filters['adherent'] = $form->get('adherent')->getData();
                $filters['town'] = $form->get('town')->getData();
                $filters['intercommunal'] = $form->get('intercommunal')->getData();
                $filters['service'] = $form->get('service')->getData();
            }
        }

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $folders = $this->em->getRepository(Folder::class)->findFolderBrowser(null,
                $filters['dateStart'], $filters['dateEnd'], $filters['num'], $filters['numFolder'], $filters['adherent'], $filters['town'], $filters['intercommunal'], $filters['service']);
        } else {
            $folders = $this->em->getRepository(Folder::class)->findFolderBrowser($adherent,
                $filters['dateStart'], $filters['dateEnd'], $filters['num'], $filters['numFolder']);
        }

        return $this->render('@LuccaFolder/Dashboard/folder.html.twig', [
            'form' => $form->createView(),
            'filters' => $filters,
            'adherent' => $adherent,
            'folders' => $folders
        ]);
    }
}
