<?php

namespace App\Entity;

use App\Repository\OrganizationRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: OrganizationRepository::class)]
class Organization
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'boolean')]
    private $isUser;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isUser(): ?bool
    {
        return $this->isUser;
    }

    public function setIsUser(bool $isUser): self
    {
        $this->isUser = $isUser;

        return $this;
    }
}
