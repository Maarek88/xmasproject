<?php

namespace App\Controller;

use App\Repository\DownloadRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DownloadController extends AbstractFOSRestController
{
    private $downloadRepository;
    private $entityManager;

    public function __construct(
        DownloadRepository $downloadRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->downloadRepository = $downloadRepository;
        $this->entityManager      = $entityManager;
    }

    /**
     * @Rest\Get("/api/download")
     * @return View
     */
    public function getAllDownloads(): View
    {
        $downloads = $this->downloadRepository->findAllNotDeleted();

        return $this->view($downloads, Response::HTTP_OK);
    }


    /**
     * @Rest\Delete("/api/download/{id}")
     * @param int $id
     * @return View
     */
    public function deleteDownload(int $id): View
    {
        $download = $this->downloadRepository->findOneById($id);

        if (!$download) {
            throw new NotFoundHttpException('Download not found');
        }

        $this->entityManager->remove($download);
        $this->entityManager->flush();

        return $this->view(null, Response::HTTP_OK);
    }
}
