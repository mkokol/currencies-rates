<?php

namespace App\Tests\Cli\Curremcy;

use App\Lib\CurrencyLayerService;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use OceanApplications\currencylayer\client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RatesTest extends KernelTestCase
{
    /**
     * @dataProvider rateScenarios
     *
     * @param $currencyTo
     * @param $apiResponseData
     * @param $commandExpectedOutput
     */
    public function testExecute(
        $currencyTo,
        $apiResponseData,
        $commandExpectedOutput
    ) {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('currency:get-rates');
        $command->setCurrencyLayerService(
            $this->getCurrencyLayerServiceMock($apiResponseData)
        );
        $command->setContainer(
            $this->getContainerMock()
        );

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            '--to'    => $currencyTo,
        ]);

        $commandOutput = $commandTester->getDisplay();
        $this->assertContains($commandExpectedOutput, $commandOutput);
    }

    public function rateScenarios()
    {
        yield [
            'ALL',
            [
                'success'   => true,
                'terms'     => 'https://currencylayer.com/terms',
                'privacy'   => 'https://currencylayer.com/privacy',
                'timestamp' => 1538568845,
                'source'    => 'USD',
                'quotes'    => [
                    'USDEUR' => 0.865845,
                    'USDGBP' => 0.769835,
                    'USDCAD' => 1.28345,
                ],
            ],
            "Fetching currencies from API and storing them in DB\n"
            . "Currency pair USD and EUR has rate: 0.865845\n"
            . "Currency pair USD and GBP has rate: 0.769835\n"
            . "Currency pair USD and CAD has rate: 1.28345\n"
            . 'Currencies are fetched and stored in DB',
        ];
        yield [
            'EUR',
            [
                'success'   => true,
                'terms'     => 'https://currencylayer.com/terms',
                'privacy'   => 'https://currencylayer.com/privacy',
                'timestamp' => 1538568845,
                'source'    => 'USD',
                'quotes'    => [
                    'USDEUR' => 0.865845,
                ],
            ],
            "Fetching currencies from API and storing them in DB\n"
            . "Currency pair USD and EUR has rate: 0.865845\n"
            . 'Currencies are fetched and stored in DB',
        ];
        yield [
            'GBP',
            [
                'success'   => true,
                'terms'     => 'https://currencylayer.com/terms',
                'privacy'   => 'https://currencylayer.com/privacy',
                'timestamp' => 1538568845,
                'source'    => 'USD',
                'quotes'    => [
                    'USDGBP' => 0.769835,
                ],
            ],
            "Fetching currencies from API and storing them in DB\n"
            . "Currency pair USD and GBP has rate: 0.769835\n"
            . 'Currencies are fetched and stored in DB',
        ];
        yield [
            'CAD',
            [
                'success'   => true,
                'terms'     => 'https://currencylayer.com/terms',
                'privacy'   => 'https://currencylayer.com/privacy',
                'timestamp' => 1538568845,
                'source'    => 'USD',
                'quotes'    => [
                    'USDCAD' => 1.28345,
                ],
            ],
            "Fetching currencies from API and storing them in DB\n"
            . "Currency pair USD and CAD has rate: 1.28345\n"
            . 'Currencies are fetched and stored in DB',
        ];
    }

    private function getCurrencyLayerServiceMock($adiResponseData)
    {
        $currencyLayerClientMock = $this->createMock(client::class);
        $currencyLayerClientMock
            ->expects($this->once())
            ->method('source')
            ->willReturn($currencyLayerClientMock);
        $currencyLayerClientMock
            ->expects($this->once())
            ->method('currencies')
            ->willReturn($currencyLayerClientMock);
        $currencyLayerClientMock
            ->expects($this->once())
            ->method('live')
            ->willReturn($adiResponseData);

        $currencyLayerServiceMock = $this
            ->getMockBuilder(CurrencyLayerService::class)
            ->setMethods(['getCurrencyLayerClient'])
            ->getMock();
        $currencyLayerServiceMock
            ->expects($this->once())
            ->method('getCurrencyLayerClient')
            ->willReturn($currencyLayerClientMock);

        return $currencyLayerServiceMock;
    }

    private function getContainerMock()
    {
        $entityManagerMock = $this->createMock(EntityManager::class);

        $registryMock = $this->createMock(Registry::class);
        $registryMock
            ->expects($this->once())
            ->method('getManager')
            ->willReturn($entityManagerMock);

        $containerMock = $this->createMock(ContainerInterface::class);
        $containerMock
            ->expects($this->once())
            ->method('get')
            ->withAnyParameters()
            ->willReturn($registryMock);

        return $containerMock;
    }
}