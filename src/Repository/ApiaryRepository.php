<?php

namespace App\Repository;

use App\Entity\Apiary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Apiary>
 *
 * @method Apiary|null find($id, $lockMode = null, $lockVersion = null)
 * @method Apiary|null findOneBy(array $criteria, array $orderBy = null)
 * @method Apiary[]    findAll()
 * @method Apiary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Apiary::class);
    }

    public function save(Apiary $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Apiary $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Retourne les informations sur les ruchers associés à l'apiculteur
     * @return Apiary[]
     */
    public function findApiariesByBeekeeper(int $idBeekeeper)
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->select('a')
            ->join('a.beekeeper', 'b')
            ->where('b.id = :beekeeperId')
            ->setParameter('beekeeperId', $idBeekeeper);
        return $qb->getQuery()->getResult();
    }


    //    /**
    //     * @return Apiary[] Returns an array of Apiary objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Apiary
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
