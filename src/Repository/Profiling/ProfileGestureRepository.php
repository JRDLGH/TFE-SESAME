<?php

namespace App\Repository\Profiling;

use App\Entity\Profiling\ProfileGesture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProfileGesture|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProfileGesture|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProfileGesture[]    findAll()
 * @method ProfileGesture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfileGestureRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProfileGesture::class);
    }


    public function findGesturesByProfileId($profileId)
    {
        return $this->createQueryBuilder('pg')
            ->join('App\Entity\Thesaurus\Gesture',' g','WITH',
                'g.id = pg.profileId')
            ->where('pg.profileId = :id')
            ->setParameters([
                'id' => $profileId
            ])
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return ProfileGesture[] Returns an array of ProfileGesture objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProfileGesture
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
