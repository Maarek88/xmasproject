<?php

namespace App\Tests;

use App\Tests\ApiTester;
use Codeception\Util\HttpCode;
use Symfony\Component\Console\Command\Command;

class DownloadFileCommandCest
{
    public function _before(ApiTester $I)
    {
    }

    public function downloadFile(ApiTester $I)
    {
        $I->runSymfonyConsoleCommand(
            'app:download-file',
            ['url' => 'https://via.placeholder.com/350x150'],
            [],
            Command::SUCCESS
        );
    }

    public function downloadFileIncorrectUrl(ApiTester $I)
    {
        $I->runSymfonyConsoleCommand('app:download-file', ['url' => 'incorrecturl'], [], Command::FAILURE);
    }

    public function downloadFileNotExistingUrl(ApiTester $I)
    {
        $I->runSymfonyConsoleCommand('app:download-file', ['url' => 'http://notexistingurl'], [], Command::FAILURE);
    }
}
