<?php

namespace App\Repository;

use App\Entity\Beehive;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Beehive>
 *
 * @method Beehive|null find($id, $lockMode = null, $lockVersion = null)
 * @method Beehive|null findOneBy(array $criteria, array $orderBy = null)
 * @method Beehive[]    findAll()
 * @method Beehive[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BeehiveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Beehive::class);
    }

    public function save(Beehive $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Beehive $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
    * Retourne les informations sur les catégories et le nombre d'articles associés.
    * @return Category[]
    */
    public function findBeehivesByApiaries(array $apiaries,int $beekeeperId)
    {
        
        // exprimer la requête en mode préparé
        $query = $this->getEntityManager()->createQuery('
        SELECT b, COUNT(b.id) n
        FROM App\Entity\Beehive b
        JOIN App\Entity\Apiary a
        WHERE b.id = a.beehive
        GROUP BY b.id
        ORDER BY n DESC
        ');
        return $query->getResult();
    }



    //    /**
    //     * @return Beehive[] Returns an array of Beehive objects
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

    //    public function findOneBySomeField($value): ?Beehive
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
