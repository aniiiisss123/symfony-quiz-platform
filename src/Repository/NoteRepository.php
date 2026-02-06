<?php
namespace App\Repository;

use App\Entity\Note;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    public function save(Note $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Note $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByEleveId(int $eleveId): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.eleve_id = :eleveId')
            ->setParameter('eleveId', $eleveId)
            ->getQuery()
            ->getResult();
    }

    public function findNoteForQuiz(int $eleveId, int $quizId): ?Note
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.eleve_id = :eleveId')
            ->andWhere('n.quiz = :quizId')
            ->setParameter('eleveId', $eleveId)
            ->setParameter('quizId', $quizId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function calculateTotalNote(int $eleveId, int $quizId): float
    {
        $result = $this->getEntityManager()->createQuery(
            'SELECT SUM(r.note_exercice) 
             FROM App\Entity\Reponse r
             JOIN r.exercice_id e
             WHERE r.eleve_id = :eleveId AND e.quiz_id = :quizId'
        )
        ->setParameter('eleveId', $eleveId)
        ->setParameter('quizId', $quizId)
        ->getSingleScalarResult();

        return (float)$result;
    }
    
}