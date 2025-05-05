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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response, ResponseHeaderBag};
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

use Lucca\Bundle\SettingBundle\Entity\Setting;
use Lucca\Bundle\SettingBundle\Form\ImportSettingType;
use Lucca\Bundle\SettingBundle\Manager\SettingManager;
use Lucca\Bundle\SettingBundle\Utils\ConverterCsvArray;

#[Route(path: '/setting')]
#[IsGranted('ROLE_ADMIN')]
class ImportSettingController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
        private readonly ConverterCsvArray $converterCsvArray,
    )
    {
    }

    /**
     * Import a new CSV File in Setting
     */
    #[Route(path: '-import', name: 'lucca_setting_import', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function importSettingController(Request $request): Response
    {
        $form = $this->createForm(ImportSettingType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->isValid()) {

                /** @var UploadedFile $file */
                $file = $form->getData()['file'];
                $mimes = ['application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv', 'application/octet-stream'];
                if (!in_array($file->getClientMimeType(), $mimes)) {
                    $this->addFlash('danger', 'flash.import.badFormat');

                    return $this->redirectToRoute('lucca_setting_import');
                }

                // Importing CSV on DB via Doctrine ORM
                $nbFail = $this->import($file->getPathname());

                if ($nbFail == 0) {
                    $this->addFlash('success', 'flash.import.createdSuccessfully');
                } else {
                    $this->addFlash('warning', 'flash.import.createdPartial');
                }

                return $this->redirectToRoute('lucca_setting_index');
            }
        }

        return $this->render('@LuccaSetting/Setting/setting-import.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Export all settings into a csv file
     */
    #[Route(path: '-export', name: 'lucca_setting_export', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function exportSettingController(): Response
    {
        // Provide a name for your file with extension
        $now = (new \DateTime('now'))->format('_d-m-Y_H-i-s');
        $filename = "settings-" . SettingManager::get('setting.general.app.name') . $now . '.csv';

        // The dynamically created content of the file
        $fileContent = $this->export();

        // Return a response with a specific content
        $response = new Response($fileContent);

        // Create the disposition of the file
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        // Set the content disposition
        $response->headers->set('Content-Disposition', $disposition);

        // Dispatch request
        return $response;
    }

    /**
     * Import from file and update objects
     */
    protected function import($file): int
    {
        /** Choice date line in csv file with offset field */
        $data = $this->converterCsvArray->easyConvert($file, ',');

        $nbFail = 0;
        /** Parse data and create object for each one */
        foreach ($data as $row) {

            /** @var Setting $setting */
            $setting = $this->em->getRepository(Setting::class)->findOneBy(['name' => $row[0]]);
            if ($setting) {
                if ($setting->getType() == Setting::TYPE_BOOL) {
                    /** Use multiple else if to fix a bug */
                    if ($row[1] == "FAUX") {
                        $setting->setValue(false);
                    } elseif ($row[1] == "VRAI") {
                        $setting->setValue(true);
                    } elseif ($row[1] == 0) {
                        $setting->setValue(false);
                    } elseif ($row[1] == 1) {
                        $setting->setValue(true);
                    }
                } else {
                    $setting->setValue($row[1]);
                }
                $this->em->persist($setting);
            } else {
                $nbFail++;
                $this->addFlash('warning', $this->translator->trans("text.importFailed", [], 'SettingBundle') . $row[0]);
            }
        }

        /** flush last items, detach all for doctrine and finish progress */
        $this->em->flush();
        $this->em->clear();

        return $nbFail;
    }

    /**
     * Export all settings and return as a string
     */
    protected function export(): string
    {
        $settings = $this->em->getRepository(Setting::class)->findAll();

        $content = "";
        foreach ($settings as $setting) {
            $content .= ($setting->getName() . "," . $setting->getValue() . ';');
        }

        return $content;
    }
}
