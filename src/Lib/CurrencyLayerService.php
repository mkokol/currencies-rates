<?php

namespace App\Lib;

use OceanApplications\currencylayer\client;

class CurrencyLayerService
{
    /**
     * @var client
     */
    private $currencyLayerClient;

    /**
     * @return client
     */
    public function getCurrencyLayerClient(): client
    {
        if (empty($this->currencyLayerClient)) {
            $currencyLayerAccessKey = getenv('CURRENCY_LAYER_ACCESS_KEY');
            $this->currencyLayerClient = new client($currencyLayerAccessKey);
        }

        return $this->currencyLayerClient;
    }

    /**
     * @param client $currencyLayerClient
     */
    public function setCurrencyLayerClient(client $currencyLayerClient): void
    {
        $this->currencyLayerClient = $currencyLayerClient;
    }

    public function fetch($from, array $to)
    {
        $adiResponse = $this->getCurrencyLayerClient()
            ->source($from)
            ->currencies(implode($to, ','))
            ->live();

        return isset($adiResponse['quotes'])
            ? $adiResponse['quotes']
            : [];
    }
}