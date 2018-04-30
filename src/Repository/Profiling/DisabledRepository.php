<?php

namespace App\Repository\Profiling;

use App\Entity\Profiling\Disabled;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Disabled|null find($id, $lockMode = null, $lockVersion = null)
 * @method Disabled|null findOneBy(array $criteria, array $orderBy = null)
 * @method Disabled[]    findAll()
 * @method Disabled[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DisabledRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Disabled::class);
    }

//    /**
//     * @return Disabled[] Returns an array of Disabled objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Disabled
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
