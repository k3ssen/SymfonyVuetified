<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UploadImageController extends AbstractController
{
    /**
     * @Route("/upload-image", name="upload_image")
     */
    public function uploadImage(Request $request) {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('file');

        $url = $error = null;

        if (stripos($uploadedFile->getMimeType(), 'image') === false) {
            $error = 'Only image files are allowed.';
        } elseif ($uploadedFile->getSize() > 2000000) {
            $error = 'This file is too large. Please choose images below 1MB.';
        } else {
            $destination = $this->getParameter('kernel.project_dir') . '/public/uploads';

            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

            $uploadedFile->move(
                $destination,
                $newFilename
            );

            $url = $request->getUriForPath("/uploads/" . $newFilename);
        }

        return new JsonResponse([
            'url' => $url,
            'error' => $error,
        ]);
    }
}