<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Controller\Api;

use Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

use Lucca\Bundle\AdherentBundle\Finder\AdherentFinder;
use Lucca\Bundle\DecisionBundle\Entity\Decision;
use Lucca\Bundle\FolderBundle\Entity\{Folder, Tag};
use Lucca\Bundle\FolderBundle\Utils\MayorLetterManager;
use Lucca\Bundle\MinuteBundle\Entity\Human;
use Lucca\Bundle\ParameterBundle\Entity\Town;

#[IsGranted('ROLE_USER')]
#[Route(path: '/')]
class MayorLetterController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AdherentFinder $adherentFinder,
        private readonly TranslatorInterface $translator,
        private readonly MayorLetterManager $mayorLetterManager,
    )
    {
    }

    /**
     * Search geocode corresponding to address
     */
    #[Route(path: '/getFolderList', name: 'lucca_mayor_letter_get_folders_api', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function getFolderListAction(Request $request): Response
    {
        /** Who is connected */
        $adherent = $this->adherentFinder->whoAmI();

        $townId = intval($request->get('terms'));

        $town = $this->em->getRepository(Town::class)->find($townId);

        try {
            $folders = $this->em->getRepository(Folder::class)->findByTown($town, $adherent);
        } catch (Exception) {
            return new Response("<tr><td colspan='14' class='text-center'>Aucun résultat</td></tr>");
        }

        if (count($folders) === 0) {
            return new Response("<tr><td colspan='14' class='text-center'>Aucun résultat</td></tr>");
        }

        /**  Initialize list  */
        $list = [];

        /**
         * This block construct and add a row to the list for each folder found
         */
        /** @var Folder $folder */
        foreach ($folders as $folder) {
            // Init minute value
            $minute = $folder->getMinute();

            // Init <tr> with id and color if minute is closed
            $row = "<tr id='" . $folder->getId() . "' ";
            if($minute->getClosure()) {
                $row .= "class='table-info'";
            } elseif ($folder->getDateClosure() !== null) {
                $row .= "class='table-primary'";
            }
            $row .= ">";

            // for checklist
            $row .= "<td></td>";

            // Set name town and num plot
            $row .= "<td>";
            $row .= $minute->getPlot()->getTown()->getName();
            $row .= "<br>";
            $row .= "<b>" . $minute->getPlot()->getParcel() . "</b>";
            $row .= "</td>";

            // set folder num
            $row .= "<td>".$folder->getNum()."</td>";

            // Set Date closure if there is one
            $row .= "<td>";
            if ($folder->getDateClosure() !== null)
                $row .= $folder->getDateClosure()->format('d/m/Y');
            $row .= "</td>";

            // Set Tags
            $row .= "<td>";
            /** @var Tag $tag */
            foreach ($folder->getTagsNature() as $tag) {
                $row .= "<a class='btn btn-xs btn-secondary'>";
                $row .= $tag->getName();
                $row .= "</a> ";
            }
            $row .= "</td>";

            // Set Human
            $row .= "<td>";
            /** @var Human $human */
            foreach ($minute->getHumans() as $human) {
                $row .= $human->getName();
                $row .= "  ";
            }
            $row .= "</td>";

            // Set Decision TGI
            $row .= "<td>";
            /** @var Decision $decision */
            foreach ($minute->getDecisions() as $decision) {
                if ($decision->getTribunalCommission() && $decision->getTribunalCommission()->getDateJudicialDesision()) {
                    $row .= $decision->getTribunalCommission()->getDateJudicialDesision()->format('d/m/Y');
                    $row .= "<br><span class='badge badge-info'>";
                    $row .= $decision->getTribunalCommission()->getStatusDecision();
                    $row .= "</span><br>";
                }
            }
            $row .= "</td>";

            // Set Decision Appeal Commision
            $row .= "<td>";
            /** @var Decision $decision */
            foreach ($minute->getDecisions() as $decision) {
                if ($decision->getAppealCommission() && $decision->getAppealCommission()->getDateJudicialDesision()) {
                    $row .= $decision->getAppealCommission()->getDateJudicialDesision()->format('d/m/Y');
                    $row .= "<br><span class='badge badge-info'>";
                    $row .= $decision->getAppealCommission()->getStatusDecision();
                    $row .= "</span><br>";
                }
            }
            $row .= "</td>";

            // Set Decision
            $row .= "<td>";
            /** @var Decision $decision */
            foreach ($minute->getDecisions() as $decision){
                if ($decision->getDateStartRecovery()) {
                    $row .= $decision->getDateStartRecovery()->format('d/m/Y');
                    $row .= "<br><span class='badge badge-info'>"
                        . $this->translator->trans("text.decision.yes", [], 'LuccaFolderBundle')
                        ."</span><br>";
                }
                else
                    $row .= "<span class='badge badge-warning'>"
                        . $this->translator->trans("text.decision.no", [], 'LuccaFolderBundle')
                        ."</span>";
            }
            $row .= "</td>";

            // Set Expulsion
            $row .= "<td>";
            /** @var Decision $decision */
            foreach ($minute->getDecisions() as $decision){
                if($decision->getExpulsion() && $decision->getExpulsion()->getDateJudicialDesision()) {
                    $row .= $decision->getExpulsion()->getDateJudicialDesision()->format('d/m/Y');
                    $row .= "<br><span class='badge badge-info'>"
                        . $this->translator->trans("text.decision.yes", [], 'LuccaFolderBundle')
                        ."</span><br>";
                }
                else
                    $row .= "<span class='badge badge-warning'>"
                        . $this->translator->trans("text.decision.no", [], 'LuccaFolderBundle')
                        ."</span>";
            }
            $row .= "</td>";

            // Set Demolition
            $row .= "<td>";
            /** @var Decision $decision */
            foreach ($minute->getDecisions() as $decision) {
                if ($decision->getDemolition() && $decision->getDemolition()->getDateDemolition()) {
                    $row .= $decision->getDemolition()->getDateDemolition()->format('d/m/Y');
                    $row .= "<br><span class='badge badge-info'>"
                        . $this->translator->trans("text.decision.yes", [], 'LuccaFolderBundle')
                        ."</span><br>";
                }
                else
                    $row .= "<span class='badge badge-warning'>"
                        . $this->translator->trans("text.decision.no", [], 'LuccaFolderBundle')
                        ."</span>";
            }
            $row .= "</td>";

            // Set Closure
            $row .= "<td>";
            if ($minute->getClosure()) {
                $row .= $minute->getClosure()->getDateClosing()->format('d/m/Y');
            }
            $row .= "</td>";

            // Action
            $row .= "<td>";
            $row .= "<a href='". $this->generateUrl('lucca_minute_show', array('id' => $minute->getId())) ."'";
            $row .= "class='btn btn-sm btn-primary'";
            $row .= "title='" . $this->translator->trans("link.minute.show", [], 'LuccaFolderBundle') . "'>";
            $row .= "<i class='fa fa-eye'></i></a>";
            $row .= "</td>";

            $row .= "</tr>";

            $list[] = $row;
        }

        $data = [];
        $data['rows'] = $list;

        return new Response(json_encode($data), 200);
    }

    /**
     * Api Action
     * Post check value for new MayorLetter
     *
     * @throws Exception
     */
    #[Route(path: '/postCheckMayorLetter', name: 'lucca_mayor_letter_check_post', options: ['expose' => true], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function apiCheckPostMayorLetter(Request $request): Response
    {
        /* get Body Request */
        $json = $request->getContent();
        $data = json_decode($json);

        /* check if all field of form is fill */
        $message = $this->mayorLetterManager->checkMayorLetterField($data);

        /* if there is at least 1 message, return an error with this message */
        if ($message !== "") {
            return new JsonResponse([
                'success' => false,
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => $message
            ], Response::HTTP_BAD_REQUEST);
        }

        /* return ok with id of dunning created */
        return new JsonResponse([
            'success' => true,
            'code' => Response::HTTP_OK,
            'message' => 'CHECKED'
        ], Response::HTTP_OK);
    }
}
