<?php

namespace App\Repository;

use App\Entity\LoginRegister;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method LoginRegister|null find($id, $lockMode = null, $lockVersion = null)
 * @method LoginRegister|null findOneBy(array $criteria, array $orderBy = null)
 * @method LoginRegister[]    findAll()
 * @method LoginRegister[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoginRegisterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoginRegister::class);
    }

    // /**
    //  * @return LoginRegister[] Returns an array of LoginRegister objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LoginRegister
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
