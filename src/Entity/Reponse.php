<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Exercice;

#[ORM\Entity]
class Reponse
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    private ?int $id = null;
    
    #[ORM\ManyToOne(targetEntity: Exercice::class)]
    #[ORM\JoinColumn(name: "exercice_id", referencedColumnName: "id_e", onDelete: "CASCADE")]
    private ?Exercice $exercice = null; 

    #[ORM\Column(type: "text")]
    private string $reponse;

    #[ORM\Column(type: "float")]
    private float $note_exercice;

    #[ORM\Column(type: "integer")]
    private int $eleve_id;
    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    
    public function getReponse()
    {
        return $this->reponse;
    }

    public function setReponse($value)
    {
        $this->reponse = $value;
    }

    public function getNote_exercice()
    {
        return $this->note_exercice;
    }

    public function setNote_exercice($value)
    {
        $this->note_exercice = $value;
    }

    public function getEleve_id()
    {
        return $this->eleve_id;
    }

    public function setEleve_id($value)
    {
        $this->eleve_id = $value;
    }
    public function getExercice(): ?Exercice
    {
        return $this->exercice;
    }

    public function setExercice(?Exercice $exercice): self
    {
        $this->exercice = $exercice;
        return $this;
    }
}
