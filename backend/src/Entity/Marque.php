<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\MarqueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MarqueRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['marque:read']],
    denormalizationContext: ['groups' => ['marque:write']],
    operations: [
        new GetCollection(),
        new Get(),
        new Post(),
        new Put(),
        new Patch(),
        new Delete(),
    ]
)]
class Marque
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['marque:read', 'vehicule:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Groups(['marque:read', 'marque:write', 'vehicule:read'])]
    private ?string $nom = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Range(min: 1800, max: 2100)]
    #[Groups(['marque:read', 'marque:write', 'vehicule:read'])]
    private ?int $anneeCreation = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Groups(['marque:read', 'marque:write', 'vehicule:read'])]
    private ?string $pays = null;

    #[ORM\OneToMany(mappedBy: 'marque', targetEntity: Vehicule::class, cascade: ['persist'])]
    #[Groups(['marque:read'])]
    private Collection $vehicules;

    public function __construct()
    {
        $this->vehicules = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function getAnneeCreation(): ?int { return $this->anneeCreation; }
    public function setAnneeCreation(int $anneeCreation): static { $this->anneeCreation = $anneeCreation; return $this; }

    public function getPays(): ?string { return $this->pays; }
    public function setPays(string $pays): static { $this->pays = $pays; return $this; }

    public function getVehicules(): Collection { return $this->vehicules; }
    public function addVehicule(Vehicule $vehicule): static
    {
        if (!$this->vehicules->contains($vehicule)) {
            $this->vehicules->add($vehicule);
            $vehicule->setMarque($this);
        }
        return $this;
    }
    public function removeVehicule(Vehicule $vehicule): static
    {
        if ($this->vehicules->removeElement($vehicule)) {
            if ($vehicule->getMarque() === $this) {
                $vehicule->setMarque(null);
            }
        }
        return $this;
    }
}
