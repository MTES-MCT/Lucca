<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, BinaryFileResponse, Response};
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use Lucca\Bundle\UserBundle\Entity\User;
use Lucca\Bundle\MediaBundle\Entity\Media;
use Lucca\Bundle\MediaBundle\Utils\ImageResizer;

#[Route(path: '/media')]
class MediaController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface        $em,
        private readonly TokenStorageInterface         $tokenStorage,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly ImageResizer                  $imageResizer,

        #[Autowire(param: 'lucca_media.upload_directory')]
        private readonly string                        $upload_dir,
    )
    {
    }

    /**
     * Media Show
     */
    #[Route(path: '/show/{p_fileName}', name: 'lucca_media_show', methods: ['GET'])]
    public function showAction($p_fileName, Request $request): Response
    {
        $newWidth = intval($request->get('width'));
        $media = $this->em->getRepository(Media::class)->findOneFileByName($p_fileName);

        /**
         * @var User $user Current user connected
         */
        $user = $this->tokenStorage->getToken()?->getUser() ?? 'anon.';

        /**
         * If media is null or
         * If User is anon and media is not public or
         * If $user is authenticated and the media is not public and the user hasn't the role ROLE_MEDIA_READ
         */
        if ($media == null ||
            ($user === "anon." && !$media->getPublic()) ||
            ($user instanceof User && !$media->getPublic() && !$this->authorizationChecker->isGranted('ROLE_MEDIA_READ'))
        ) {
            throw new AccessDeniedException('');
        }

        if ($newWidth) {

            $imageResized = $this->imageResizer->resizeWidth($media, $newWidth);

            if ($imageResized) {
                $headers = ['Content-Type' => $media->getMimeType(),
                    'Content-Disposition' => 'inline; filename="' . $media->getName() . '"'];
                ob_start();
                $this->imageResizer->imagePrintAccordingToMime($media, $imageResized);
                $str = ob_get_clean();

                return new Response($str, 200, $headers);
            }
        }

        /** Check if file exist */
        $fullFilePath = $this->upload_dir . $media->getFilePath();
        if (!file_exists($fullFilePath)) {
            throw new NotFoundHttpException('File not found', null, 404);
        }

        return new BinaryFileResponse($fullFilePath);
    }
}
