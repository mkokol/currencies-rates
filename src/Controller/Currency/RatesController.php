<?php

namespace App\Controller\Currency;

use App\Entity\CurrencyRate;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Annotations;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Annotations\Route(
 *     service="App\Controller\Currency\RatesController"
 * )
 */
class RatesController extends Controller
{
    /**
     * @Annotations\Route(
     *     "/api/v1/currency/rates",
     *     name="currency_rates"
     * )
     * @Annotations\Method("GET")
     */
    public function getAction(Request $request)
    {
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'asc');

        try {
            $currencyRates = $this->getDoctrine()
                ->getRepository(CurrencyRate::class)
                ->findAllSortedByField($sortBy, $sortOrder);
        } catch (Exception $exception) {
            return $this->json([], 422);
        }

        return $this->json([
            'rates' => array_map(function (CurrencyRate $currencyRate) {
                return $currencyRate->toArray();
            }, $currencyRates),
        ]);
    }
}
