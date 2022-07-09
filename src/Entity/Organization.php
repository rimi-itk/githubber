<?php

namespace App\Entity;

use App\Repository\OrganizationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints\NotNull;

#[ORM\Entity(repositoryClass: OrganizationRepository::class)]
class Organization
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[NotNull]
    private $name;

    #[ORM\Column(type: 'boolean')]
    private $isUser;

    #[ORM\OneToMany(mappedBy: 'organization', targetEntity: Repository::class, orphanRemoval: true)]
    private $repositories;

    public function __construct()
    {
        $this->repositories = new ArrayCollection();
    }

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

    public function isUser(): ?bool
    {
        return $this->isUser;
    }

    public function setIsUser(bool $isUser): self
    {
        $this->isUser = $isUser;

        return $this;
    }

    /**
     * @return Collection<int, Repository>
     */
    public function getRepositories(): Collection
    {
        return $this->repositories;
    }

    public function addRepository(Repository $repository): self
    {
        if (!$this->repositories->contains($repository)) {
            $this->repositories[] = $repository;
            $repository->setOrganization($this);
        }

        return $this;
    }

    public function removeRepository(Repository $repository): self
    {
        if ($this->repositories->removeElement($repository)) {
            // set the owning side to null (unless already changed)
            if ($repository->getOrganization() === $this) {
                $repository->setOrganization(null);
            }
        }

        return $this;
    }
}
