<?php

namespace App\Repository;

use App\Entity\SmsReference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SmsReference>
 *
 * @method SmsReference|null find($id, $lockMode = null, $lockVersion = null)
 * @method SmsReference|null findOneBy(array $criteria, array $orderBy = null)
 * @method SmsReference[]    findAll()
 * @method SmsReference[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SmsReferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SmsReference::class);
    }

    //    /**
    //     * @return SmsReference[] Returns an array of SmsReference objects
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

    //    public function findOneBySomeField($value): ?SmsReference
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
