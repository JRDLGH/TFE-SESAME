<?php

namespace App\Repository;

use App\Entity\Thesaurus\Gesture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Gesture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Gesture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Gesture[]    findAll()
 * @method Gesture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GestureRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Gesture::class);
    }

    public function findAllPublished()
    {
        $dql = "SELECT g 
                  FROM App\Entity\Thesaurus\Gesture g
                WHERE g.isPublished = 1
                ORDER BY g.id";

        $query = $this->getEntityManager()->createQuery($dql);

        return $query->execute();

    }

    public function findPublishedById($id){
        $dql= "SELECT g
                  FROM App\Entity\Thesaurus\Gesture g
               WHERE g.id = :id AND g.isPublished = 1";

        $query= $this->getEntityManager()->createQuery($dql);
        $query->setParameters([
            'id' => $id
        ])->setMaxResults(1);

        return $query->execute();
    }

    public function findByTagNameExcludeNameBeginBy($tag){

        $dql = "SELECT g
                  FROM App\Entity\Thesaurus\Gesture g 
                    JOIN g.tags as t
                WHERE t.keyword like :tag AND g.name not like :tag AND g.isPublished = 1
                GROUP BY g.id";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters([
            'tag' => $tag.'%'
        ]);

        return $query->execute();

    }

    public function findByNameBeginBy($name){
        $dql = "SELECT g
                  FROM App\Entity\Thesaurus\Gesture g
                WHERE g.name like :name AND g.isPublished = 1";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters([
            'name' => $name.'%',
        ]);

         return $query->execute();

    }
//    /**
//     * @return Gesture[] Returns an array of Gesture objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Gesture
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
