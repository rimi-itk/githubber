<?php

namespace App\Entity;

use App\Repository\RepositoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\SoftDeleteable;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\NotNull;

#[ORM\Entity(repositoryClass: RepositoryRepository::class)]
#[UniqueEntity(fields: ['name', 'organization'])]
#[SoftDeleteable]
class Repository
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[NotNull]
    private ?string $name;

    #[ORM\Column(type: 'json', nullable: true)]
    private $data = [];

    #[ORM\ManyToOne(targetEntity: Organization::class, inversedBy: 'repositories')]
    #[ORM\JoinColumn(nullable: false)]
    private $organization;

    #[ORM\Column(type: 'boolean')]
    private $ignored = false;

    public function __toString(): string
    {
        return $this->name;
    }

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

    public function getOwner(): ?string
    {
        return $this->owner;
    }

    public function setOwner(string $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(?array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    public function isIgnored(): ?bool
    {
        return $this->ignored;
    }

    public function setIgnored(?bool $ignored): self
    {
        $this->ignored = $ignored;

        return $this;
    }
}
