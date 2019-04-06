<?php

namespace App\Repository;

use App\Entity\Thesaurus\Gesture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

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

    /**
     * Find gesture that match all given tags.
     * @tags a String containing tags, separated by a space
     */
    public function findByTagNameExcludeNameBeginBy($tags){

        $tagsArray  = explode(' ',$tags);
        $nbTags     = count($tagsArray);
        $lastTag    = '';

        if($nbTags > 1){
            $lastTag = array_pop($tagsArray) . '%';
            $tagList = implode(',',$tagsArray);
        }else{
            $tagList = $tags;
        }

        /**
         * Search different values on same column
         * @see https://stackoverflow.com/questions/28595648/select-rows-that-have-two-different-values-in-same-column
         */
        $sql = "select g.id, g.name, g.profile_video, g.video, g.cover, g.description, g.creation_date, g.is_published, g.publication_date, g.updated_at
                from gesture as g,
                gesture_tag as gt,
                    (select id from tag where keyword IN (:list) OR keyword like :last) as search 
                where gt.tag_id = search.id
                and g.id = gesture_id
                group by gesture_id
                having count(tag_id) = :nb";

        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('App\Entity\Thesaurus\Gesture', 'g');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameters([
            'list'  => $tagList,
            'last'  => $lastTag,
            'nb'    => $nbTags
        ]);

        return $query->execute();

    }

    public function findByNameBeginBy($name,$limit = null){
        $dql = "SELECT g
                  FROM App\Entity\Thesaurus\Gesture g
                WHERE g.name like :name AND g.isPublished = 1";
        $query = $this->getEntityManager()->createQuery($dql);

        $query->setParameters([
            'name' => $name.'%',
        ]);
        if($limit){
            $query->setMaxResults($limit);
        }

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
