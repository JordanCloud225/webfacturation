<?php

namespace App\Repository;

use App\Entity\Objetdepense;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Objetdepense>
 */
class ObjetdepenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Objetdepense::class);
    }

    public function getListeObjetdepense(array $criteria,int $entreprise, $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->createQueryBuilder('n');
        $qb->where('n.deletedAt is NULL');
        $qb->andWhere('n.identreprise = :entreprise');
        $qb->setParameter('entreprise', $entreprise);

        if(array_key_exists('search', $criteria)){
            $qb->andWhere($qb->expr()->like('n.libelle',':search'))
                ->setParameter('search', '%' .$criteria['search']. '%');
            unset($criteria['search']);

        }
        $qb->orderBy('n.libelle', $orderBy);

        if(null !== $limit){
            $qb->setMaxResults($limit);
        }

        if(null !== $offset){
            $qb->setFirstResult($offset);
        }

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    //    /**
    //     * @return Objetdepense[] Returns an array of Objetdepense objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Objetdepense
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
