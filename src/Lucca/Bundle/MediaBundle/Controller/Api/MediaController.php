<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Controller\Api;

use Exception;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, JsonResponse, File\UploadedFile, File\File, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\MediaBundle\Entity\{Category, Media, MediaAsyncInterface, MediaListAsyncInterface, MetaDataModel};
use Lucca\Bundle\MediaBundle\Manager\FileManager;

#[Route(path: '/media')]
#[IsGranted('ROLE_USER')]
class MediaController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly RouterInterface $router,
        private readonly FileManager $fileManager,
    )
    {
    }

    #[Route(path: '-get-meta-datas', name: 'lucca_media_api_meta_datas', options: ['expose' => true], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getMetaDatasAction(Request $request): Response
    {
        $categoryId = $request->get("categoryId");

        $category = $this->em->getRepository(Category::class)->find($categoryId);
        $metaModels = $category->getMetasDatasModels();

        $metaDatasSuggestions = [];

        /** @var MetaDataModel $metaModel */
        foreach ($metaModels as $metaModel) {
            $metaDatasSuggestions[$metaModel->getKeyword()] = $metaModel->getName();
        }

        return new JsonResponse($metaDatasSuggestions);
    }

    /**
     * TODO Change function to get metamodels by extension ( for a new media )
     */
    #[Route(path: '-get-meta-datas-by-extension', name: 'lucca_media_api_meta_datas_by_extension', options: ['expose' => true], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getMetaModelsByExtensionAction(Request $request): Response
    {
        $categoryId = $request->get("extension");

        $category = $this->em->getRepository(Category::class)->find($categoryId);
        $metaModels = $category->getMetasDatasModels();

        $metaDatasSuggestions = [];

        /** @var MetaDataModel $metaModel */
        foreach ($metaModels as $metaModel) {
            $metaDatasSuggestions[$metaModel->getKeyword()] = $metaModel->getName();
        }

        return new JsonResponse($metaDatasSuggestions);
    }

    /**
     *  Upload Media Files
     *
     * @throws ORMException
     */
    #[Route(path: '-upload-files', name: 'lucca_media_api_upload_files', options: ['expose' => true], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function uploadFiles(Request $request): Response
    {
        // Get files
        $files = $_FILES;
        $filesUploaded = [];

        // Loop on files to construct a file array
        foreach ($files as $file) {
            // with the dropzone plugin, name is an array -> $_FILES['file'][]
            if (is_array($file['name'])) {
                for ($i = 0; $i < count($file['name']); $i++) {
                    $filesUploaded[] = [
                        'isUploaded' => false,
                        'file' => [
                            'tmp_name' => $file['tmp_name'][$i],
                            'name' => $file['name'][$i],
                            'type' => $file['type'][$i],
                            'size' => $file['size'][$i],
                            'error' => $file['error'][$i],
                        ]
                    ];
                }
            // without it, name is a string because it's $_FILES[]
            } else {
                $filesUploaded[] = [
                    'isUploaded' => false,
                    'file' => [
                        'tmp_name' => $file['tmp_name'],
                        'name' => $file['name'],
                        'type' => $file['type'],
                        'size' => $file['size'],
                        'error' => $file['error'],
                    ]
                ];
            }
        }

        // Loop on files
        foreach ($filesUploaded as $key => $file) {
            /** @var File $fileToUpload */
            $fileToUpload = new UploadedFile($file['file']['tmp_name'],
                $file['file']['name'],
                $file['file']['type'],
                $file['file']['size'],
                $file['file']['error']);

            if ($request->get('uploadMedia')) {
                $media = new Media();
                // Upload file on tmp folder
                $media = $this->fileManager->uploadFile($fileToUpload, $media);

                // Update the var isUploaded to send this information
                $filesUploaded[$key]['isUploaded'] = ($media instanceof Media);
                if ($media instanceof Media) {
                    $filesUploaded[$key]['url'] = $this->router->generate('lucca_media_show', ['p_fileName' => $media->getNameCanonical()]);

                    /** If the request come from a view that need a public media */
                    if ($request->request->get('public')) {
                        $media->setPublic(true);
                    }
                    $this->em->persist($media);
                    $this->em->flush();
                }
            } else {
                // Upload file on tmp folder
                $isUploaded = $this->fileManager->uploadTmpFile($fileToUpload);

                // Update the var isUploaded to send this information
                $filesUploaded[$key]['isUploaded'] = $isUploaded;
            }
        }

        return new JsonResponse($filesUploaded);
    }

    /**
     *  Get a delete modal for Media
     */
    #[Route(path: '-get-delete-modal', name: 'lucca_media_api_get_delete_modal', options: ['expose' => true], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getDeleteModal(Request $request): Response
    {
        // Get Media Id
        $mediaId = $request->get('id');

        // Get Entity props of the media
        $entityParent = $request->get('entityParent');
        $idParent = $request->get('idParent');
        $varsMedia = $request->get('varsMedia');

        // If media is not found return code -1 ( Id null )
        // Try to get media by id and removed file if exist
        if (!($mediaId !== '' && $mediaId !== null)) {
            return new JsonResponse(-1);
        }

        $media = $this->em->getRepository(Media::class)->find($mediaId);

        // If media is not found return code 2 ( Media not found )
        if ($media === null || $media->getId() === null) {
            return new JsonResponse(2);
        }

        // Test if media is not empty
        if ($media->isEmpty()) {
            // If media found is empty return code 3 ( File not found )
            return new JsonResponse(3);
        }

        /**
         * Return media delete form
         */
        return  $this->render('@LuccaMedia/Media/Admin/modal-delete.html.twig', [
            'media' => $media,
            'entityParent' => $entityParent,
            'idParent' => $idParent,
            'varsMedia' => $varsMedia,
        ]);
    }

    /**
     *  Remove Media
     */
    #[Route(path: '-remove-media', name: 'lucca_media_api_remove_media', options: ['expose' => true], methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function removeMedia(Request $request): Response
    {
        // Get Media Id and Entity Parent parameters
        $mediaId = $request->get('id');

        // Get Entity props of the media
        $entityParent = $request->get('entityParent');
        $idParent = $request->get('idParent');
        $varsMedia = $request->get('varsMedia');

        // If media is not found return code -1 ( Id null )
        // Try to get media by id and removed file if exist
        if (!($mediaId !== '' && $mediaId !== null)) {
            return new JsonResponse(-1);
        }

        $media = $this->em->getRepository(Media::class)->find($mediaId);

        // If media is not found return code 2 ( Media not found )
        if ($media === null || $media->getId() === null) {
            return new JsonResponse(2);
        }

        // Test if media is not empty
        if ($media->isEmpty()) {
            // If media found is empty return code 3 ( File not found )
            return new JsonResponse(3);
        }

        /** Find if Media is used by a parent entity */
        if ($idParent && $entityParent) {
            /** @entityParent $entity */
            $entity = $this->em->getRepository($entityParent)->find($idParent);

            if ($entity instanceof $entityParent && $entity instanceof MediaListAsyncInterface) {
                // Get all medias on this entity
                $entityMedias = $entity->getAsyncMedias();

                // entityMedias has to be an ArrayCollection to use getValues()
                if (count($entityMedias) > 0){
                    // Filter the array to get medias with the same id
                    $mediaEntity = array_filter($entityMedias->getValues(), function($mediaEntity) use ($media){
                        return $mediaEntity && $mediaEntity->getId() === $media->getId();
                    });

                    // Taking the first element cause medias should not have same id.
                    // $mediaEntity should be 0 or 1 length
                    $firstMediaEntity = array_pop($mediaEntity);

                    // Test if the media search is a Media and remove of the entity list
                    if ($firstMediaEntity instanceof Media) {
                        $entity->removeAsyncMedia($firstMediaEntity, $varsMedia);
                    }

                    $this->em->persist($entity);
                }
            }
            // Test if the entity is a MediaAsyncInterface
            // Use getAsyncMedia to test if we found the media
            if ($entity instanceof $entityParent
                && $entity instanceof MediaAsyncInterface
                && $entity->getAsyncMedia() instanceof Media
                && $entity->getAsyncMedia()->getId() === $media->getId()) {

                    // Remove the media on the entity
                    $entity->setAsyncMedia();
                    $this->em->persist($entity);
            }
        }

        // Remove file of the Media
        $this->fileManager->removeFile($media);

        // Remove Media
        $this->em->remove($media);

        // Try to flush if the remove not failed due to a foreign key condition
        try{
            $this->em->flush();
            // return code 1 ( Remove success )
            return new JsonResponse(1);
        } catch (Exception $e) {
            // return code 0 ( Remove failed )
            return new JsonResponse(0);
        }
    }
}
