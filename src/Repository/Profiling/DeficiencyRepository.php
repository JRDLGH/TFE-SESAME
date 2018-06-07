<?php

namespace App\Repository\Profiling;

use App\Entity\Profiling\Deficiency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Deficiency|null find($id, $lockMode = null, $lockVersion = null)
 * @method Deficiency|null findOneBy(array $criteria, array $orderBy = null)
 * @method Deficiency[]    findAll()
 * @method Deficiency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeficiencyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Deficiency::class);
    }

//    /**
//     * @return Deficiency[] Returns an array of Deficiency objects
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
    public function findOneBySomeField($value): ?Deficiency
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
