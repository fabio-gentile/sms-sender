<?php

namespace App\Repository;

use App\Entity\Sms;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sms>
 *
 * @method Sms|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sms|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sms[]    findAll()
 * @method Sms[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SmsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sms::class);
    }

    /**
     * Get all unset sms.
     * @return array|null
     */
    public function findUnsetSms() : ?array
    {
        return $this->createQueryBuilder('s')
            ->where('s.sentAt IS NULL')
            ->andWhere('s.status <> :status')
            ->setParameter('status', 'CANCELLED')
            ->orWhere('s.status IS NULL')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get the latests sms.
     * @return array|null
     */
    public function findLatests() : ?array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get the total of sms.
     * @return int|null
     */
    public function findCountSms() : ?int
    {
        return $this->createQueryBuilder('s')
            ->select('COUNT(s)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    //    /**
    //     * @return Sms[] Returns an array of Sms objects
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

    //    public function findOneBySomeField($value): ?Sms
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
