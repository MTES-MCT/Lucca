<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Controller\Admin;

use App\Lucca\Bundle\MinuteBundle\Manager\MinuteStoryManager;
use Doctrine\ORM\EntityManagerInterface;
use Lucca\Bundle\FolderBundle\Entity\Folder;
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Lucca\Bundle\MinuteBundle\Entity\Updating;
use Lucca\Bundle\MinuteBundle\Form\UpdatingType;
use Lucca\Bundle\MinuteBundle\Generator\NumUpdatingGenerator;
use Lucca\Bundle\MinuteBundle\Utils\HtmlCleaner;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/minute-{minute_id}/updating')]
#[IsGranted('ROLE_LUCCA')]
class UpdatingController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MinuteStoryManager     $minuteStoryManager,
        private readonly HtmlCleaner            $htmlCleaner,
        private readonly NumUpdatingGenerator   $numUpdatingGenerator
    ) {
    }

    #[Route('/new', name: 'lucca_updating_new', methods: ['GET', 'POST'])]
    public function newAction(#[MapEntity(id: 'minute_id')] Minute $minute): RedirectResponse
    {
        $em = $this->entityManager;;

        $updating = new Updating();
        $updating->setMinute($minute);
        $updating->setNum($this->numUpdatingGenerator->generate($updating));

        /** update status of the minute */
        $this->minuteStoryManager->manage($minute);

        $this->addFlash('success', 'flash.updating.createdSuccessfully');
        $em->persist($updating);
        $em->flush();

        return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId(), '_fragment' => 'updating-' . $updating->getId()));
    }

    #[Route('-{id}/step-1', name: 'lucca_updating_step1', methods: ['GET', 'POST'])]
    public function step1Action(Request $request, #[MapEntity(id: 'minute_id')] Minute $minute, #[MapEntity(id: 'id')] Updating $updating): RedirectResponse|Response
    {
        $em = $this->entityManager;;
        $form = $this->createForm(UpdatingType::class, $updating, array());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** Clean html from useless font and empty span */
            $updating->setDescription($this->htmlCleaner->removeAllFonts($updating->getDescription()));

            $em->persist($updating);
            $em->flush();

            $this->addFlash('success', 'flash.updating.updatedSuccessfully');

            if ($request->request->get('saveAndContinue') !== null)
                return $this->redirectToRoute('lucca_updating_folder_new', array(
                    'minute_id' => $updating->getMinute()->getId(), 'updating_id' => $updating->getId()
                ));

            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId(), '_fragment' => 'updating-' . $updating->getId()));
        }

        return $this->render('@LuccaMinute/Updating/step1.html.twig', array(
            'updating' => $updating,
            'minute' => $minute,
            'form' => $form->createView(),
        ));
    }

    #[Route('-{id}/delete', name: 'lucca_updating_delete', methods: ['DELETE'])]
    public function deleteAction(Request $request, #[MapEntity(id: 'minute_id')] Minute $minute, #[MapEntity(id: 'id')] Updating $updating): RedirectResponse
    {
        $em = $this->entityManager;;

        $em->remove($updating);
        $em->flush();

        $this->minuteStoryManager->manage($minute);
        $em->flush();

        $this->addFlash('danger', 'flash.updating.deletedSuccessfully');
        return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
    }

    /**
     * Creates a form to delete a Folder entity.
     *
     * @param Minute $minute
     * @param Folder $folder
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Minute $minute, Folder $folder)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_updating_delete', array('minute_id' => $minute->getId(), 'id' => $folder->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
