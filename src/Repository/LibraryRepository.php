<?php
declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Library;

/**
 * @method Library find($id, $lockMode = null, $lockVersion = null)
 * @method Library findOneBy(array $criteria, array $orderBy = null)
 * @method Library[] findAll()
 * @method Library[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LibraryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Library::class);
    }

    public function createBasicQueryBuilder(): QueryBuilder
    {
        return parent::createQueryBuilder('library');
    }
}
