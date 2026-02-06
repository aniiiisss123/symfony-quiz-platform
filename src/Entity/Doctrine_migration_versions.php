<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
class Doctrine_migration_versions
{

    #[ORM\Id]
    #[ORM\Column(type: "string", length: 191)]
    private string $version;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $executed_at;

    #[ORM\Column(type: "integer")]
    private int $execution_time;

    public function getVersion()
    {
        return $this->version;
    }

    public function setVersion($value)
    {
        $this->version = $value;
    }

    public function getExecuted_at()
    {
        return $this->executed_at;
    }

    public function setExecuted_at($value)
    {
        $this->executed_at = $value;
    }

    public function getExecution_time()
    {
        return $this->execution_time;
    }

    public function setExecution_time($value)
    {
        $this->execution_time = $value;
    }
}
