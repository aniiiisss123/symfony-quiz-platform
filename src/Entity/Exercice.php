<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Entity\Quiz;
use App\Entity\Reponse;

#[ORM\Entity]
class Exercice
{
    #[ORM\Id]
    #[ORM\GeneratedValue] 
    #[ORM\Column(type: "integer")]
    private ?int $id_e = null;

    #[ORM\ManyToOne(targetEntity: Quiz::class, inversedBy: "exercices")]
    #[ORM\JoinColumn(name: 'quiz_id', referencedColumnName: 'quiz_id', onDelete: 'CASCADE')]
    private Quiz $quiz_id;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank(message: "The question cannot be blank.")]
    #[Assert\Length(
        min: 10,
        max: 500,
        minMessage: "The question must be at least {{ limit }} characters long.",
        maxMessage: "The question cannot be longer than {{ limit }} characters."
    )]
    private string $question;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank(message: "Options cannot be blank.")]
    #[Assert\Regex(
        pattern: '/^[^,]+(,[^,]+){2,}$/',
        message: "Please provide at least 3 options separated by commas."
    )]
    private string $options;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "Score cannot be blank.")]
    #[Assert\Positive(message: "Score must be a positive number.")]
    #[Assert\LessThanOrEqual(
        value: 10,
        message: "Score cannot be greater than {{ compared_value }}."
    )]
    private int $score;

    #[ORM\Column(name: "isMandatory", type: "boolean")]
    private bool $isMandatory;

    #[ORM\Column(name: "correctAnswer", type: "string", length: 255)]
    #[Assert\NotBlank(message: "The correct answer cannot be blank.")]
    #[Assert\Length(
        min: 1,
        max: 255,
        minMessage: "The correct answer cannot be shorter than {{ limit }} characters.",
        maxMessage: "The correct answer cannot be longer than {{ limit }} characters."
    )]
    private string $correctAnswer;

    #[ORM\Column(name: "imagePath", type: "string", length: 255, nullable: true)]

    #[Assert\Length(
        max: 255,
        maxMessage: "The image path cannot be longer than {{ limit }} characters."
    )]
    private string $imagePath;

    #[ORM\OneToMany(mappedBy: "exercice", targetEntity: Reponse::class)]
    private Collection $reponses;

    public function __construct()
    {
        $this->reponses = new ArrayCollection();
    }

    public function getId_e()
    {
        return $this->id_e;
    }

    public function setId_e($value)
    {
        $this->id_e = $value;
    }

    public function getQuiz_id()
    {
        return $this->quiz_id;
    }

    public function setQuiz_id($value)
    {
        $this->quiz_id = $value;
    }

    public function getQuestion()
    {
        return $this->question;
    }

    public function setQuestion($value)
    {
        $this->question = $value;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions($value)
    {
        $this->options = $value;
    }

    public function getScore()
    {
        return $this->score;
    }

    public function setScore($value)
    {
        $this->score = $value;
    }

    public function getIsMandatory()
    {
        return $this->isMandatory;
    }

    public function setIsMandatory($value)
    {
        $this->isMandatory = $value;
    }

    public function getCorrectAnswer()
    {
        return $this->correctAnswer;
    }
    
    public function setCorrectAnswer($value)
    {
        $this->correctAnswer = $value;
    }

    

    public function setImagePath(?string $imagePath): void
    {
        $this->imagePath = $imagePath;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function getReponses(): Collection
    {
        return $this->reponses;
    }

    public function addReponse(Reponse $reponse): self
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses[] = $reponse;
            $reponse->setExercice($this);
        }
        return $this;
    }

    /**
     * Custom validation to ensure correctAnswer is one of the options.
     *
     * @Assert\Callback
     */
    public function validateCorrectAnswerInOptions(ExecutionContextInterface $context): void
    {
        $optionsArray = array_map('trim', explode(',', $this->options)); 
        if (!in_array($this->correctAnswer, $optionsArray, true)) {
            $context->buildViolation('The correct answer must match one of the provided options.')
                ->atPath('correctAnswer') 
                ->addViolation();
        }
    }

}