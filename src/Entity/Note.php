<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Quiz;

#[ORM\Entity]
class Note
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Quiz::class, inversedBy: "notes")]
    #[ORM\JoinColumn(name: 'quizId', referencedColumnName: 'quiz_id', onDelete: 'CASCADE')]
    private ?Quiz $quiz = null;

    #[ORM\Column(type: "integer")]
    private int $eleve_id;

    #[ORM\Column(type: "float")]
    private float $note_totale;

    #[ORM\Column(type: "integer", nullable: true, name: "time_spent ")]
    private ?int $timeSpent = null;
    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getEleve_id()
    {
        return $this->eleve_id;
    }

    public function setEleve_id($value)
    {
        $this->eleve_id = $value;
    }

    public function getNote_totale()
    {
        return $this->note_totale;
    }

    public function setNote_totale($value)
    {
        $this->note_totale = $value;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): self
    {
        $this->quiz = $quiz;
        return $this;
    }

    public function getTimeSpent(): ?int
    {
        return $this->timeSpent;
    }

    public function setTimeSpent(?int $timeSpent): void
    {
        $this->timeSpent = $timeSpent;
    }
}