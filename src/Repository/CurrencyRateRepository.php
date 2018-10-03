<?php

namespace App\Repository;

use App\Entity\CurrencyRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CurrencyRate|null find($id, $lockMode = null, $lockVersion = null)
 * @method CurrencyRate|null findOneBy(array $criteria, array $orderBy = null)
 * @method CurrencyRate[]    findAll()
 * @method CurrencyRate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrencyRateRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CurrencyRate::class);
    }

    /**
     * @param $sortBy
     * @param $sortOrder
     * @return CurrencyRate[] Returns an array of CurrencyRate objects
     * @throws \Exception
     */
    public function findAllSortedByField($sortBy, $sortOrder)
    {
        if (
            !in_array($sortBy, ['id', 'currency_from', 'currency_to', 'rate'])
            || !in_array($sortOrder, ['asc', 'desc'])
        ) {
            throw new \Exception(sprintf(
                'Parameters sortBy:%s and sortOrder:%s are not supported',
                $sortBy,
                $sortOrder
            ));
        }

        $query = $this->createQueryBuilder('c_r')
            ->orderBy('c_r.' . $sortBy, $sortOrder)
            ->setMaxResults(10)
            ->getQuery();

        return $query->getResult();
    }
}
