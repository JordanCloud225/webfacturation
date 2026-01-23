<?php

namespace App\Repository;

use App\Entity\Fournisseur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Fournisseur>
 */
class FournisseurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fournisseur::class);
    }
    public function getListeFournisseur(array $criteria,int $entreprise, $orderBy = null, $limit = null, $offset = null): array
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
    //     * @return Fournisseur[] Returns an array of Fournisseur objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Fournisseur
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
