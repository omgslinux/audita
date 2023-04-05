<?php

namespace App\Repository;

use App\Entity\Programm;
use App\Entity\BudgetYear;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Programm>
 *
 * @method Programm|null find($id, $lockMode = null, $lockVersion = null)
 * @method Programm|null findOneBy(array $criteria, array $orderBy = null)
 * @method Programm[]    findAll()
 * @method Programm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgrammRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Programm::class);
    }

    public function save(Programm $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Programm $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByYearExceptProgramms(BudgetYear $year, $programms): array
    {
        if (!is_array($programms)) {
            $programms = [$programms];
        }
        $excluded = [];
        foreach ($this->findByYear(['year' => $year], ['code' => 'ASC']) as $p) {
            if (array_search($p->getCode(), $programms)===false) {
                $excluded[] = $p;
            }
        }

        return $excluded;
    }

//    /**
//     * @return Programm[] Returns an array of Programm objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Programm
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
