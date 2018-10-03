<?php

namespace App\Controller\Currency;

use App\Entity\CurrencyRate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Annotations;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Annotations\Route(
 *     service="App\Controller\Currency\RateController"
 * )
 */
class RateController extends Controller
{
    /**
     * @Annotations\Route(
     *     "/api/v1/currency/rate",
     *     name="delete_currency_rate"
     * )
     * @Annotations\Method("DELETE")
     */
    public function getAction(Request $request)
    {
        $id = $request->get('id');
        $entityManager = $this->getDoctrine()->getEntityManager();
        $currencyRate = $entityManager
            ->getRepository(CurrencyRate::class)
            ->find($id);

        if (empty($currencyRate)) {
            return $this->json([], 422);
        }

        $entityManager->remove($currencyRate);
        $entityManager->flush();

        return $this->json([]);
    }
}
