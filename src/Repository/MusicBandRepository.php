<?php

namespace App\Repository;

use App\Entity\MusicBand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MusicBand>
 *
 * @method MusicBand|null find($id, $lockMode = null, $lockVersion = null)
 * @method MusicBand|null findOneBy(array $criteria, array $orderBy = null)
 * @method MusicBand[]    findAll()
 * @method MusicBand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MusicBandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MusicBand::class);
    }

//    /**
//     * @return MusicBand[] Returns an array of MusicBand objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MusicBand
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
