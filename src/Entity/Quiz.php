<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
class Quiz
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    private ?int $quiz_id = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "The title cannot be blank.")]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "The title must be at least {{ limit }} characters long.",
        maxMessage: "The title cannot be longer than {{ limit }} characters."
    )]
    private string $title;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank(message: "The description cannot be blank.")]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "The description must be at least {{ limit }} characters long.",
        maxMessage: "The description cannot be longer than {{ limit }} characters."
    )]
    private string $description;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "The duration cannot be blank.")]
    #[Assert\Positive(message: "The duration must be a positive integer.")]
    #[Assert\Range(
        min: 1,
        max: 20,
        notInRangeMessage: "The duration must be between {{ min }} and {{ max }} minutes."
    )]
    private int $duration;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "The total score cannot be blank.")]
    #[Assert\Positive(message: "The total score must be a positive integer.")]
    private int $totalscore;

    #[ORM\Column(type: "datetime")]    
    #[Assert\NotBlank(message: "The total score cannot be blank.")]

    private ?\DateTimeInterface $creationdate = null;

    #[ORM\Column(type: "string", length: 100)]
    #[Assert\NotBlank(message: "The author cannot be blank.")]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: "The author's name must be at least {{ limit }} characters long.",
        maxMessage: "The author's name cannot be longer than {{ limit }} characters."
    )]
    private string $author;

    #[ORM\OneToMany(mappedBy: "quiz_id", targetEntity: Exercice::class, cascade: ["persist", "remove"])]
    private Collection $exercices;

    public function __construct()
    {
        $this->exercices = new ArrayCollection();
    }

    public function getQuiz_Id(): ?int
    {
        return $this->quiz_id;
    }
    public function setQuiz_Id(int $quiz_id): self
    {
        $this->quiz_id = $quiz_id;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;
        return $this;
    }

    public function getTotalscore(): int
    {
        return $this->totalscore;
    }

    public function setTotalscore(int $totalscore): self
    {
        $this->totalscore = $totalscore;
        return $this;
    }

    public function getCreationdate(): ?\DateTimeInterface
    {
        return $this->creationdate;
    }

    public function setCreationdate(?\DateTimeInterface $creationdate): self
    {
        $this->creationdate = $creationdate;
        return $this;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;
        return $this;
    }

    public function getExercices(): Collection
    {
        return $this->exercices;
    }

    public function addExercice(Exercice $exercice): self
    {
        if (!$this->exercices->contains($exercice)) {
            $this->exercices[] = $exercice;
            $exercice->setQuiz_Id($this);
        }

        return $this;
    }

    public function removeExercice(Exercice $exercice): self
    {
        if ($this->exercices->removeElement($exercice)) {
            if ($exercice->getQuiz_Id() === $this) {
                $exercice->setQuiz_Id(null);
            }
        }

        return $this;
    }
}