<?php

namespace App\Repository;

use App\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Person>
 */
class PersonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Person::class);
    }

    public function findPersonsPaginated(
        int $page,
        int $limit,
        ?string $sort = null,
        ?string $order = null,
        ?string $person = null
    ): array
    {
        $order = $order ?? 'ASC';

        $qb = $this->createQueryBuilder('p');

        if ($person !== null) {
            $normalizedPerson = $this->normalize($person);
            $qb->where('LOWER(p.firstName) LIKE :person')
                ->orWhere('LOWER(p.lastName) LIKE :person')
                ->orWhere('LOWER(p.email) LIKE :person')
                ->orWhere('LOWER(p.phone) LIKE :person')
                ->setParameter('person', '%' . $normalizedPerson . '%');
        }

        switch ($sort) {
            case 'firstName':
                $qb->orderBy('p.firstName', $order);
                break;
            case 'lastName':
                $qb->orderBy('p.lastName', $order);
                break;
            case 'phone':
                $qb->orderBy('p.phone', $order);
                break;
            case 'email':
                $qb->orderBy('p.email', $order);
                break;
        }

        // Compte total des résultats
        $countQb = clone $qb;
        $countQb->select('COUNT(DISTINCT p.id)');
        $total = (int)$countQb->getQuery()->getSingleScalarResult();

        // Apply pagination
        $qb->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return [
            'items' => $qb->getQuery()->getResult(),
            'total' => $total,
        ];
    }


    private function normalize(string $str): string
    {
        $normalized = strtolower(trim($str));
        return preg_replace('/\s+/', ' ', $normalized);
    }
}
