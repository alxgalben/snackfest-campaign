<?php

namespace App\Repository;

use App\Entity\ReceiptRegistration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReceiptRegistration>
 *
 * @method ReceiptRegistration|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReceiptRegistration|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReceiptRegistration[]    findAll()
 * @method ReceiptRegistration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReceiptRegistrationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReceiptRegistration::class);
    }

public function weekReceiptsCounter(string $phoneNumber, \DateTimeInterface $date): int
{
    $startOfWeek = (new \DateTime())->setISODate($date->format('Y'), $date->format('W'))->setTime(0, 0, 0);
    $endOfWeek = (clone $startOfWeek)->modify('+7 days')->setTime(23, 59, 59);

    /*$startOfWeek = new \DateTime('first day of this week');
    $startOfWeek = new \DateTime('last day of this week');*/

    return $this->createQueryBuilder('r')
        ->select('COUNT(r.id)')
        ->where('r.phoneNumber = :phoneNumber')
        ->andWhere('r.submittedAt >= :startOfWeek AND r.submittedAt <= :endOfWeek')
        ->setParameter('phoneNumber', $phoneNumber)
        ->setParameter('startOfWeek', $startOfWeek->format('Y-m-d H:i:s'))
        ->setParameter('endOfWeek', $endOfWeek->format('Y-m-d H:i:s'))
        ->getQuery()
        ->getSingleScalarResult();
}

    public function add(ReceiptRegistration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ReceiptRegistration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ReceiptRegistration[] Returns an array of ReceiptRegistration objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ReceiptRegistration
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
