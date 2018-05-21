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

    public function findByProfile($tag,$profileId)
    {
        $dql = "SELECT pg 
                    FROM App\Entity\Profiling\ProfileGesture as pg
                        JOIN pg.gesture as g
                        JOIN g.tags as t
                WHERE t.keyword like :tag AND g.name not like :tag AND g.isPublished = 1 AND pg.profile = :profileId
                GROUP BY g.id";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters([
            'tag' => $tag.'%',
            'profileId' => $profileId
        ]);

        return $query->execute();
    }

    public function findByNameBeginBy($name,$profileId,$limit = null)
    {
        $dql = "SELECT pg 
                    FROM App\Entity\Profiling\ProfileGesture as pg
                        JOIN pg.gesture as g 
                WHERE g.name like :name  AND g.isPublished = 1 AND pg.profile = :profileId
                GROUP BY g.id";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters([
            'name' => $name.'%',
            'profileId' => $profileId
        ]);

        if($limit){
            $query->setMaxResults($limit);
        }

        return $query->execute();
    }

    public function findByLearningDate($limit,$order,$profileId)
    {
        $qb = $this->createQueryBuilder('pg')
            ->where('pg.id = :profileId')
            ->orderBy('pg.learningDate DESC')
            ->getQuery()
            ->setParameters([
                'profileId' => $profileId
            ])
            ->setMaxResults(10)
            ->execute();

        return $qb;
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
