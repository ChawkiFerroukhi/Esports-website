<?php

namespace App\Repository;

use App\Entity\Jeux;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Jeux|null find($id, $lockMode = null, $lockVersion = null)
 * @method Jeux|null findOneBy(array $criteria, array $orderBy = null)
 * @method Jeux[]    findAll()
 * @method Jeux[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JeuxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Jeux::class);
    }
function couuunt()
{
    $conn=$this->getEntityManager()->getConnection();
    $sql='select jeux.id,jeux.nom,jeux.description, round(AVG(review.note),1) as e 
from jeux left JOIN review ON (review.jeux_id_id=jeux.id) 
    left join equipe_jeux on (jeux.id=equipe_jeux.jeux_id) group by (jeux.id) order by e desc; ';
       $stmt=$conn->prepare($sql);
    return $stmt->executeQuery()->fetchAllAssociative();

}

    function couuuntee()
    {
        $conn=$this->getEntityManager()->getConnection();
        $sql='select jeux.id,jeux.nom,jeux.description, round(AVG(review.note),1) as e 
from jeux left JOIN review ON (review.jeux_id_id=jeux.id) 
    left join equipe_jeux on (jeux.id=equipe_jeux.jeux_id) group by (review.jeux_id_id) order by e ASC; ';
        $stmt=$conn->prepare($sql);
        return $stmt->executeQuery()->fetchAllAssociative();

    }




    // /**
    //  * @return Jeux[] Returns an array of Jeux objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Jeux
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}