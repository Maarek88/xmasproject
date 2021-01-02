<?php

namespace App\Command;

use App\Entity\Download;
use App\Service\DownloadService;
use App\Service\ImageOptimizerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DownloadFileCommand extends Command
{
    protected static $defaultName = 'app:download-file';

    private $imageOptimizerService;
    private $downloadService;
    private $entityManager;
    private $downloadPath;


    public function __construct(
        ImageOptimizerService $imageOptimizerService,
        DownloadService $downloadService,
        EntityManagerInterface $entityManager,
        string $downloadPath
    ) {
        $this->imageOptimizerService = $imageOptimizerService;
        $this->downloadService       = $downloadService;
        $this->entityManager         = $entityManager;
        $this->downloadPath          = $downloadPath;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Download file from url given in command argument')
            ->setHelp(
                'Download file from url given in command argument. 
                If the file is image and larger than 1000x1000px, make a thumbnail 1000x1000 and
                save it.'
            )
            ->addArgument('url', InputArgument::REQUIRED, 'Provide file url to download.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $output->writeln('Please provide a valid url');
            return Command::FAILURE;
        }

        $urlComponents = parse_url($url);
        $filename = basename($url);
        $filePathInfo = pathinfo($filename);
        $downloadFilePath = $this->downloadPath.uniqid().(!empty($filePathInfo['extension']) && strpos(
                $urlComponents['host'],
                '.'.$filePathInfo['extension']
            ) === false ? '.'.$filePathInfo['extension'] : '');

        if ($this->downloadService->downloadFromUrl($url, $downloadFilePath)) {
            $this->imageOptimizerService->resize($downloadFilePath);

            $download = new Download();
            $download->setFilePath($downloadFilePath);
            $download->setFileUrl($url);

            $this->entityManager->persist($download);
            $this->entityManager->flush();

            $output->writeln('Whoa! Downloaded file from '.$url);
            return Command::SUCCESS;
        }

        $output->writeln('Cannot download file from '.$url);
        return Command::FAILURE;
    }
}