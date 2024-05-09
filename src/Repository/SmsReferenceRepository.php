<?php

namespace App\Repository;

use App\Entity\Sms;
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

    /**
     * Get all sms references by sms
     * @param Sms $sms
     * @return array|null
     */
    public function findAllBySms(Sms $sms) : ?array
    {
        return $this->createQueryBuilder('s')
            ->where('s.Sms = :sms')
            ->setParameter('sms', $sms)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get all sent sms references by sms
     * @param Sms $sms
     * @return array|null
     */
    public function findAllSentSms(Sms $sms) : ?array
    {
        return $this->createQueryBuilder('s')
            ->where('s.Sms = :sms')
            ->setParameter('sms', $sms)
            ->andWhere('s.status = :status')
            ->setParameter('status', 'SENT')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get all users for the sms.
     * @param Sms $sms
     * @return array|null
     */
    public function findAllUsersBySms(Sms $sms) : ?array
    {
        return $this->createQueryBuilder('s')
            ->where('s.Sms = :sms')
            ->setParameter('sms', $sms)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get total count of all sms references by sms.
     * @param Sms $sms
     * @return int|null
     */
    public function findCountSms(Sms $sms) : ?int
    {
        return $this->createQueryBuilder('s')
            ->select('COUNT(s)')
            ->where('s.Sms = :sms')
            ->setParameter('sms', $sms)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Delete all records by sms
     * @param Sms $sms
     * @return bool
     */
    public function deleteBySms(Sms $sms): bool
    {
        return $this->createQueryBuilder('s')
            ->delete()
            ->where('s.Sms = :sms')
            ->setParameter('sms', $sms)
            ->getQuery()
            ->execute();
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
