<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Controller\Document;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\AdherentBundle\Finder\LogoFinder;
use Lucca\Bundle\MinuteBundle\Entity\Control;
use Lucca\Bundle\MinuteBundle\Entity\ControlEdition;
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Lucca\Bundle\SettingBundle\Manager\SettingManager;

#[Route('/minute-{minute_id}/control-')]
#[IsGranted('ROLE_LUCCA')]
class ControlController extends AbstractController
{
    /** Setting if use agent of refresh or minute agent */
    private mixed $useRefreshAgentForRefreshSignature;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LogoFinder $logoFinder
    )
    {
        $this->useRefreshAgentForRefreshSignature = SettingManager::get('setting.folder.useRefreshAgentForRefreshSignature.name');
    }


    /*************************** Convocation ***************************/

    #[Route('{id}/letter-access', name: 'lucca_control_access', methods: ['GET'])]
    #[IsGranted('ROLE_LUCCA')]
    public function accessLetterAction(#[MapEntity(id: 'minute_id')] Minute $minute, Control $control): Response
    {
        $em = $this->entityManager;;

        $controlEdition = $em->getRepository(ControlEdition::class)->findOneBy(
            array('control' => $control)
        );

        if ($this->useRefreshAgentForRefreshSignature)
            $agent = $control->getAgent();
        else
            $agent = $minute->getAgent();

        return $this->render('@LuccaMinute/Control/access.html.twig', array(
            'agent' => $agent,
            'minute' => $minute,
            'adherent' => $minute->getAdherent(),
            'control' => $control,
            'controlEdition' => $controlEdition,
            'officialLogo' => $this->logoFinder->findLogo($minute->getAdherent())
        ));
    }

    /*************************** Convocation ***************************/

    #[Route('{id}/letter-convocation', name: 'lucca_control_letter', methods: ['GET'])]
    #[IsGranted('ROLE_LUCCA')]
    public function convocationLetterAction(#[MapEntity(id: 'minute_id')] Minute $minute, Control $control): Response
    {
        $em = $this->entityManager;;

        $controlEdition = $em->getRepository(ControlEdition::class)->findOneBy(
            array('control' => $control)
        );

        return $this->render('@LuccaMinute/Control/convocation.html.twig', array(
            'minute' => $minute,
            'adherent' => $minute->getAdherent(),
            'control' => $control,
            'controlEdition' => $controlEdition,
            'officialLogo' => $this->logoFinder->findLogo($minute->getAdherent())
        ));
    }
}
