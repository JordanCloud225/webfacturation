<?php

namespace App\Repository;

use App\Entity\Typearticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Typearticle>
 */
class TypearticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Typearticle::class);
    }
    public function getListeTypearticle(array $criteria,int $entreprise, $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->createQueryBuilder('n');
        $qb->where('n.deletedAt is NULL');
        $qb->andWhere('n.identreprise = :entreprise');
        $qb->setParameter('entreprise', $entreprise);

        if(array_key_exists('search', $criteria)){
            $qb->andWhere($qb->expr()->like('n.libellefr',':search'))
                ->setParameter('search', '%' .$criteria['search']. '%');
            unset($criteria['search']);

        }
        $qb->orderBy('n.libellefr', $orderBy);

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
    //     * @return Typearticle[] Returns an array of Typearticle objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Typearticle
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
