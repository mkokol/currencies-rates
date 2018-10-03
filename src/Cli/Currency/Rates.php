<?php

namespace App\Cli\Currency;

use App\Entity\CurrencyRate;
use App\Lib\CurrencyLayerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Rates extends Command
{
    const CURRENCY_FROM = 'USD';
    const CURRENCY_TO   = ['EUR', 'GBP', 'CAD'];

    /**
     * @var CurrencyLayerService
     */
    private $currencyLayerService;
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @return CurrencyLayerService
     */
    public function getCurrencyLayerService(): CurrencyLayerService
    {
        return $this->currencyLayerService;
    }

    /**
     * @required
     * @param CurrencyLayerService $currencyLayerService
     */
    public function setCurrencyLayerService(CurrencyLayerService $currencyLayerService): void
    {
        $this->currencyLayerService = $currencyLayerService;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * @required
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    protected function configure()
    {
        $cliDescription = sprintf(
            'Fetch currency rates (%s to [%s]) from API and save them in DB',
            self::CURRENCY_FROM,
            implode(self::CURRENCY_TO, ', ')
        );
        $paramToDescription = sprintf(
            'With which currency we should fetch a rate %s to (ALL, %s)?',
            self::CURRENCY_FROM,
            implode(self::CURRENCY_TO, ', ')
        );

        $this->setName('currency:get-rates')
            ->setDescription($cliDescription)
            ->addOption(
                'to',
                null,
                InputArgument::OPTIONAL,
                $paramToDescription,
                'ALL'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Fetching currencies from API and storing them in DB');
        $currencyRatesToParam = strtoupper($input->getOption('to'));

        if ($currencyRatesToParam == 'ALL') {
            $currencyRatesTo = self::CURRENCY_TO;
        } elseif (in_array($currencyRatesToParam, self::CURRENCY_TO)) {
            $currencyRatesTo = [$currencyRatesToParam];
        } else {
            throw new \Exception(sprintf(
                'Option "to" could not be "%s".',
                $currencyRatesToParam
            ));
        }

        $rates = $this->getCurrencyLayerService()->fetch(
            self::CURRENCY_FROM,
            $currencyRatesTo
        );
        $entityManager = $this->getContainer()
            ->get('doctrine')
            ->getManager();

        foreach ($currencyRatesTo as $currencyTo) {
            $currencyToRate = $rates[self::CURRENCY_FROM . $currencyTo];

            $output->writeln(sprintf(
                'Currency pair %s and %s has rate: %s',
                self::CURRENCY_FROM,
                $currencyTo,
                $currencyToRate
            ));
            $currencyRate = new CurrencyRate();
            $currencyRate->setCurrencyFrom(self::CURRENCY_FROM);
            $currencyRate->setCurrencyTo($currencyTo);
            $currencyRate->setRate($currencyToRate);
            $currencyRate->setCreatedAt(new \DateTime('now'));

            $entityManager->persist($currencyRate);
        }

        $entityManager->flush();
        $output->writeln('Currencies are fetched and stored in DB');
    }
}