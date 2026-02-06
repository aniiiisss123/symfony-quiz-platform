<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
class Messenger_messages
{

    #[ORM\Id]
    #[ORM\Column(type: "bigint")]
    private string $id;

    #[ORM\Column(type: "text")]
    private string $body;

    #[ORM\Column(type: "text")]
    private string $headers;

    #[ORM\Column(type: "string", length: 190)]
    private string $queue_name;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $created_at;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $available_at;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $delivered_at;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($value)
    {
        $this->body = $value;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function setHeaders($value)
    {
        $this->headers = $value;
    }

    public function getQueue_name()
    {
        return $this->queue_name;
    }

    public function setQueue_name($value)
    {
        $this->queue_name = $value;
    }

    public function getCreated_at()
    {
        return $this->created_at;
    }

    public function setCreated_at($value)
    {
        $this->created_at = $value;
    }

    public function getAvailable_at()
    {
        return $this->available_at;
    }

    public function setAvailable_at($value)
    {
        $this->available_at = $value;
    }

    public function getDelivered_at()
    {
        return $this->delivered_at;
    }

    public function setDelivered_at($value)
    {
        $this->delivered_at = $value;
    }
}
