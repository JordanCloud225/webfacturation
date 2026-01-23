<?php

namespace App\Repository;

use App\Entity\Conditionoffre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conditionoffre>
 */
class ConditionoffreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conditionoffre::class);
    }

    //    /**
    //     * @return Conditionoffre[] Returns an array of Conditionoffre objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Conditionoffre
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function getListeCondition(array $criteria, $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->createQueryBuilder('n');
        $qb->where('n.deletedAt is NULL');
        //si y'a lieu de faire de filtre, se referer a objetdepenserepository
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
}
