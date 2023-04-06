<?php

namespace App\Repository;

use App\Entity\Subconcept;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subconcept>
 *
 * @method Subconcept|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subconcept|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subconcept[]    findAll()
 * @method Subconcept[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubconceptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subconcept::class);
    }

    public function save(Subconcept $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Subconcept $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByYearExcept(BudgetYear $year, $items): array
    {
        if (!is_array($items)) {
            $items = [$items];
        }
        $excluded = [];
        foreach ($this->findByYear(['year' => $year], ['code' => 'ASC']) as $item) {
            if (array_search($item->getCode(), $items)===false) {
                $excluded[] = $item;
            }
        }

        return $excluded;
    }


//    /**
//     * @return Subconcept[] Returns an array of Subconcept objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Subconcept
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
