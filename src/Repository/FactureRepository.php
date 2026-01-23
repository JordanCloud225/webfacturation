<?php

namespace App\Repository;

use App\Entity\Facture;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<Facture>
 */
class FactureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Facture::class);
    }

    public function getListeFacture(array $criteria,int $entreprise, $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->createQueryBuilder('n');
        $qb->where('n.deletedAt is NULL');
        $qb->andWhere('n.identreprise = :entreprise');
        $qb->setParameter('entreprise', $entreprise);

        if(array_key_exists('search', $criteria)){
            $qb->andWhere($qb->expr()->like('n.numfacture',':search'))
                ->setParameter('search', '%' .$criteria['search']. '%');
            unset($criteria['search']);

        }
        $qb->orderBy('n.numfacture', $orderBy);

        if(null !== $limit){
            $qb->setMaxResults($limit);
        }

        if(null !== $offset){
            $qb->setFirstResult($offset);
        }

        $result = $qb->getQuery()->getResult();

        return $result;
    }



    public function rechercheFacture($criteria = [], DateTime $dateDebut = null, DateTime $dateFin = null, $limit = NULL)
    {
        try {
            $qb = $this->createQueryBuilder('enf');

            $qb->where("enf.deletedAt IS NULL");
            $qb->andWhere("enf.entreprise =:entreprise");
            $qb->setParameter('entreprise', $criteria['entreprise']);
            //            $qb->addOrderBy('enf.date2', 'DESC');
            //Tri en fonction des dates debut et fin
            if ($dateDebut && $dateFin) {
                $qb
                    ->andWhere('enf.datedebut BETWEEN :dateDebut AND :dateFin')
                    ->setParameter('dateDebut', $dateDebut)
                    ->setParameter('dateFin', $dateFin);
            }
           

            if ($limit) {
                $qb->setMaxResults($limit);
            }

            $query = $qb->getQuery();
            return $query->getResult();
        } catch (Exception $exc) {
            ob_start();
            echo $exc->getMessage();
            $content = ob_get_clean();
            file_put_contents("erreur_rfigerche_figurer.txt", $content . "\n", FILE_APPEND);
            return [];
        }
    }

    public function getListeFactureImpayee(int $entreprise, $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->createQueryBuilder('n');
        $qb->where('n.deletedAt is NULL');
        $qb->andWhere('n.identreprise = :entreprise');
        $qb->andWhere('n.typefacture = 1');
        $qb->setParameter('entreprise', $entreprise);

     
        $qb->orderBy('n.numfacture', $orderBy);

        if(null !== $limit){
            $qb->setMaxResults(3);
        }

        if(null !== $offset){
            $qb->setFirstResult($offset);
        }

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    
    public function getListeFacturePayee(int $entreprise, $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->createQueryBuilder('n');
        $qb->where('n.deletedAt is NULL');
        $qb->andWhere('n.identreprise = :entreprise');
        $qb->andWhere('n.typefacture = 0');
        $qb->setParameter('entreprise', $entreprise);

     
        $qb->orderBy('n.numfacture', $orderBy);

        if(null !== $limit){
            $qb->setMaxResults(3);
        }

        if(null !== $offset){
            $qb->setFirstResult($offset);
        }

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    //    /**
    //     * @return Facture[] Returns an array of Facture objects
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

    //    public function findOneBySomeField($value): ?Facture
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findOneByFacture($id): ?Facture {
        return $this->createQueryBuilder('f')
                        ->where('f.id = :id')
                        ->setParameter('id', $id)
                        ->getQuery()
                        ->getOneOrNullResult();
    }

}
