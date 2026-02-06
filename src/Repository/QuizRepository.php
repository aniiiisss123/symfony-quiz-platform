<?php
namespace App\Repository;

use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class QuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

    public function save(Quiz $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Quiz $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findLatest(int $limit = 5): array
    {
        return $this->createQueryBuilder('q')
            ->orderBy('q.creationDate', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findByAuthor(string $author): array
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.author = :author')
            ->setParameter('author', $author)
            ->orderBy('q.creationDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}