<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * Retrieve events for the specified year (fromDate and toDate within the year)
     * @return Event[]
     */
    public function findByYear(int $year): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('YEAR(e.fromDate) = :year')
            ->andWhere('YEAR(e.toDate) = :year')
            ->setParameter('year', $year)
            ->orderBy('e.fromDate', 'ASC')
            ->getQuery()->getResult();
    }


    public function findPaginated(
        int     $page = 1,
        int     $limit = 50,
        ?string $sort = null,
        ?string $order = null,
        ?string $name = null
    ): array
    {
        $order = $order ?? 'ASC';

        $qb = $this->createQueryBuilder('e');

        if ($name !== null) {
            $qb->andWhere('LOWER(e.name) LIKE :name')
                ->setParameter('name', '%' . mb_strtolower($name) . '%');
        }

        // Apply sorting
        if ($sort !== null) {
            $qb->orderBy('e.' . $sort, $order);
        }

        // Compte total des résultats
        $countQb = clone $qb;
        $countQb->select('COUNT(DISTINCT e.id)');
        $total = (int)$countQb->getQuery()->getSingleScalarResult();

        // Apply pagination
        $qb->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return [
            'items' => $qb->getQuery()->getResult(),
            'total' => $total,
        ];
    }

    //    /**
    //     * @return Event[] Returns an array of Event objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Event
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
