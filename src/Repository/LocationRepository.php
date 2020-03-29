<?php
declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Location;

/**
 * @method Location find($id, $lockMode = null, $lockVersion = null)
 * @method Location findOneBy(array $criteria, array $orderBy = null)
 * @method Location[] findAll()
 * @method Location[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

    public function createBasicQueryBuilder(): QueryBuilder
    {
        return parent::createQueryBuilder('location');
    }
}
