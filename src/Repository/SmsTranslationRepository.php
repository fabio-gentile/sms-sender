<?php

namespace App\Repository;

use App\Entity\Sms;
use App\Entity\SmsTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SmsTranslation>
 *
 * @method SmsTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method SmsTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method SmsTranslation[]    findAll()
 * @method SmsTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SmsTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SmsTranslation::class);
    }

    /**
     * Find the sms content by the language
     * @param Sms $sms
     * @param string $language
     * @return string|null
     */
    public function findContentByLanguage(Sms $sms, string $language) : ?string
    {
        return $this->createQueryBuilder('s')
            ->select('s.content')
            ->where('s.sms = :sms')
            ->setParameter('sms', $sms)
            ->andWhere('s.language = :language')
            ->setParameter('language', $language)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Delete all records by sms
     * @param Sms $sms
     * @return bool
     */
    public function deleteContentSms(Sms $sms): bool
    {
        return $this->createQueryBuilder('s')
            ->delete()
            ->where('s.sms = :sms')
            ->setParameter('sms', $sms)
            ->getQuery()
            ->execute();
    }

    //    /**
    //     * @return SmsTranslation[] Returns an array of SmsTranslation objects
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

    //    public function findOneBySomeField($value): ?SmsTranslation
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
