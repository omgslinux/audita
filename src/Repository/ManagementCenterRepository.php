<?php

namespace App\Repository;

use App\Entity\ManagementCenter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ManagementCenter>
 *
 * @method ManagementCenter|null find($id, $lockMode = null, $lockVersion = null)
 * @method ManagementCenter|null findOneBy(array $criteria, array $orderBy = null)
 * @method ManagementCenter[]    findAll()
 * @method ManagementCenter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ManagementCenterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ManagementCenter::class);
    }

    public function save(ManagementCenter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ManagementCenter $entity, bool $flush = false): void
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
//     * @return ManagementCenter[] Returns an array of ManagementCenter objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ManagementCenter
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
