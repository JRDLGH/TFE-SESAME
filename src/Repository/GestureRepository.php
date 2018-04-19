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
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT g.id, g.name, t.keyword
                  FROM gesture as g natural join tag as t 
                  WHERE g.is_published is true';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();

    }

    public function findByTagName($tag){
        $conn = $this->getEntityManager()->getConnection();
        if(is_array($tag)){
            //request for severals
        }else{
            $sql = "SELECT gt.gesture_id, gt.tag_id, t.keyword, g.name, g.description, g.cover
                        FROM gesture_tag as gt
                            JOIN tag as t
                                ON gt.tag_id = t.id
                            JOIN gesture as g
                                ON g.id = gt.gesture_id
                    WHERE t.keyword like :tag
                    ORDER BY t.id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':tag' => '%'.$tag.'%']);
            return $stmt->fetchAll();
        }

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
