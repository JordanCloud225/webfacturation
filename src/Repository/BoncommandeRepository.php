<?php

namespace App\Repository;

use App\Entity\Boncommande;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<Boncommande>
 */
class BoncommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Boncommande::class);
    }
    public function getListeBoncommande(array $criteria, int $entreprise, $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->createQueryBuilder('n');
        $qb->where('n.deletedAt is NULL');
        $qb->andWhere('n.typecommande = 3');
        $qb->andWhere('n.identreprise = :entreprise');
        $qb->setParameter('entreprise', $entreprise);



        if (array_key_exists('search', $criteria)) {
            $qb->andWhere($qb->expr()->like('n.po', ':search'))
                ->setParameter('search', '%' . $criteria['search'] . '%');
            unset($criteria['search']);
        }
        $qb->orderBy('n.po', $orderBy);

        if (null !== $limit) {
            $qb->setMaxResults($limit);
        }

        if (null !== $offset) {
            $qb->setFirstResult($offset);
        }

        $result = $qb->getQuery()->getResult();

        return $result;
    }


    public function getListeDevis(array $criteria, int $entreprise, $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->createQueryBuilder('n');
        $qb->where('n.deletedAt is NULL');
        $qb->andWhere('n.typecommande = 1');
        $qb->andWhere('n.identreprise = :entreprise');
        $qb->setParameter('entreprise', $entreprise);

        if (array_key_exists('search', $criteria)) {
            $qb->andWhere($qb->expr()->like('n.id', ':search'))
                ->setParameter('search', '%' . $criteria['search'] . '%');
            unset($criteria['search']);
        }
        $qb->orderBy('n.id', $orderBy);

        if (null !== $limit) {
            $qb->setMaxResults($limit);
        }

        if (null !== $offset) {
            $qb->setFirstResult($offset);
        }

        $result = $qb->getQuery()->getResult();

        return $result;
    }



    public function getListeProforma(array $criteria, int $entreprise, $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->createQueryBuilder('n');
        $qb->where('n.deletedAt is NULL');
        $qb->andWhere('n.typecommande = 2');
        $qb->andWhere('n.identreprise = :entreprise');
        $qb->setParameter('entreprise', $entreprise);

        if (array_key_exists('search', $criteria)) {
            $qb->andWhere($qb->expr()->like('n.id', ':search'))
                ->setParameter('search', '%' . $criteria['search'] . '%');
            unset($criteria['search']);
        }
        $qb->orderBy('n.id', $orderBy);

        if (null !== $limit) {
            $qb->setMaxResults($limit);
        }

        if (null !== $offset) {
            $qb->setFirstResult($offset);
        }

        $result = $qb->getQuery()->getResult();

        return $result;
    }
    public function findOneById(int $id): ?Boncommande
    {
        return $this->createQueryBuilder('f')
            ->where('f.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }



    public function rechercheBon($criteria = [], DateTime $dateDebut = null, DateTime $dateFin = null, $limit = NULL)
    {
        try {
            $qb = $this->createQueryBuilder('enf');

            $qb->where("enf.deletedAt IS NULL");
            $qb->andWhere("enf.identreprise =:entreprise");
            $qb->setParameter('entreprise', $criteria['entreprise']);
            //            $qb->addOrderBy('enf.date2', 'DESC');
            //Tri en fonction des dates debut et fin
            if ($dateDebut && $dateFin) {
                $qb
                    ->andWhere('enf.datedebut BETWEEN :dateDebut AND :dateFin')
                    ->setParameter('dateDebut', $dateDebut)
                    ->setParameter('dateFin', $dateFin);
            }
            if (array_key_exists('typecommande', $criteria)) {

                $qb->andWhere("enf.typecommande = :typecommande");
                $qb->setParameter('typecommande', $criteria['typecommande']);
                unset($criteria['typecommande']);
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

    //    /**
    //     * @return Boncommande[] Returns an array of Boncommande objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    public function getFactureattente(int $entreprise, $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->createQueryBuilder('n');
        $qb->where('n.deletedAt is NULL');
        $qb->andWhere('n.identreprise = :entreprise');
        $qb->andWhere('n.typefacture == 1');
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

    // Dans BonCommandeRepository.php
    public function findlastsavetoday($entreprise)
    {
        $todayStart = new \DateTime('today');
        $todayEnd = new \DateTime('tomorrow');

        return $this->createQueryBuilder('bc')
            ->where('bc.identreprise = :entreprise')
            ->andWhere('bc.deletedAt IS NULL')
            ->andWhere('bc.createdAt BETWEEN :todayStart AND :todayEnd')
            ->setParameter('entreprise', $entreprise)
            ->setParameter('todayStart', $todayStart)
            ->setParameter('todayEnd', $todayEnd)
            ->orderBy('bc.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
