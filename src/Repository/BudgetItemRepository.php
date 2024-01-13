<?php

namespace App\Repository;

use App\Entity\BudgetItem;
use App\Entity\BudgetChapter;
use App\Entity\BudgetYear;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BudgetItem>
 *
 * @method BudgetItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method BudgetItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method BudgetItem[]    findAll()
 * @method BudgetItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BudgetItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BudgetItem::class);
    }

    public function save(BudgetItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BudgetItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByChapter(BudgetChapter $BudgetChapter): array
    {
        return $this->findByChapterNumber($BudgetChapter->getCode());
    }

    public function findByChapterNumber(int $chapter): array
    {
        $qb = $this->createQueryBuilder('b')
            ->join('b.subconcept', 's')
            ->andWhere('s.code >= :min')
            ->andWhere('s.code < :max')
            ->setParameter('min', $chapter * 10000)
            ->setParameter('max', ($chapter+1) * 10000)
            ->orderBy('b.id', 'ASC')
            ->getQuery()
            //->getResult()
        ;
        return $qb->getResult();
    }

    public function findByCenterAndChapterCode($center, $chapter): array
    {
        $qb = $this->createQueryBuilder('b')
            ->join('b.center', 'c')
            ->join('b.subconcept', 's')
            ->andWhere('c.code = :code')
            ->andWhere('s.code >= :min')
            ->andWhere('s.code < :max')
            ->setParameter('code', $center)
            ->setParameter('min', $chapter * 10000)
            ->setParameter('max', ($chapter+1) * 10000)
            ->orderBy('b.year', 'ASC')
            ->getQuery()
            //->getResult()
        ;
        return $qb->getResult();
    }

    public function findByCenterAndProgrammCode($center, $programm): array
    {
        $qb = $this->createQueryBuilder('b')
            ->join('b.center', 'c')
            ->join('b.programm', 'p')
            ->andWhere('c.code = :ccode')
            ->andWhere('p.code = :pcode')
            ->setParameter('ccode', $center)
            ->setParameter('pcode', $programm)
            ->orderBy('b.year', 'ASC')
            ->getQuery()
            //->getResult()
        ;
        return $qb->getResult();
    }

    public function findByCenterAndSubconceptCode($center, $subconcept): array
    {
        $qb = $this->createQueryBuilder('b')
            ->join('b.center', 'c')
            ->join('b.subconcept', 's')
            ->andWhere('c.code = :ccode')
            ->andWhere('s.code = :scode')
            ->setParameter('ccode', $center)
            ->setParameter('scode', $subconcept)
            ->orderBy('b.year', 'ASC')
            ->getQuery()
            //->getResult()
        ;
        return $qb->getResult();
    }

    public function findByCenterCode($code): array
    {
        $qb = $this->createQueryBuilder('b')
            ->join('b.center', 'c')
            ->andWhere('c.code = :code')
            ->setParameter('code', $code)
            ->orderBy('b.year', 'ASC')
            ->getQuery()
            //->getResult()
        ;
        return $qb->getResult();
    }

    public function findByChapterCode($chapter): array
    {
        $qb = $this->createQueryBuilder('b')
            ->join('b.subconcept', 's')
            ->andWhere('s.code >= :min')
            ->andWhere('s.code < :max')
            ->setParameter('min', $chapter * 10000)
            ->setParameter('max', ($chapter+1) * 10000)
            ->orderBy('b.year', 'ASC')
            ->getQuery()
            //->getResult()
        ;
        return $qb->getResult();
    }

    public function findByProgramCode($code): array
    {
        $qb = $this->createQueryBuilder('b')
            ->join('b.programm', 'p')
            ->andWhere('p.code = :code')
            ->setParameter('code', $code)
            ->orderBy('b.year', 'ASC')
            ->getQuery()
            //->getResult()
        ;
        //dump($qb->getResult());
        return $qb->getResult();
    }

    public function findByProgramAndChapterCode($programm, $chapter): array
    {
        $qb = $this->createQueryBuilder('b')
            ->join('b.programm', 'p')
            ->join('b.subconcept', 's')
            ->andWhere('p.code = :code')
            ->andWhere('s.code >= :min')
            ->andWhere('s.code < :max')
            ->setParameter('code', $programm)
            ->setParameter('min', $chapter * 10000)
            ->setParameter('max', ($chapter+1) * 10000)
            ->orderBy('b.year', 'ASC')
            ->getQuery()
            //->getResult()
        ;
        return $qb->getResult();
    }

    public function findByProgramAndSubconceptCode($programm, $subconcept): array
    {
        $qb = $this->createQueryBuilder('b')
            ->join('b.programm', 'p')
            ->join('b.subconcept', 's')
            ->andWhere('p.code = :pcode')
            ->andWhere('s.code = :scode')
            ->setParameter('pcode', $programm)
            ->setParameter('scode', $subconcept)
            ->orderBy('b.year', 'ASC')
            ->getQuery()
            //->getResult()
        ;
        return $qb->getResult();
    }

    public function findBySubconceptCode($code): array
    {
        $qb = $this->createQueryBuilder('b')
            ->join('b.subconcept', 's')
            ->andWhere('s.code = :code')
            ->setParameter('code', $code)
            ->orderBy('b.year', 'ASC')
            ->getQuery()
            //->getResult()
        ;
        return $qb->getResult();
    }

    public function findByYearChapterNumber(BudgetYear $year, int $chapter): array
    {
        $qb = $this->createQueryBuilder('b')
            ->join('b.subconcept', 's')
            ->andWhere('s.code >= :min')
            ->andWhere('s.code < :max')
            ->andWhere('b.year = :year')
            ->setParameter('min', $chapter * 10000)
            ->setParameter('max', ($chapter+1) * 10000)
            ->setParameter('year', $year)
            ->orderBy('b.id', 'ASC')
            ->getQuery()
            //->getResult()
        ;
        return $qb->getResult();
    }

//    /**
//     * @return BudgetItem[] Returns an array of BudgetItem objects
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

//    public function findOneBySomeField($value): ?BudgetItem
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
