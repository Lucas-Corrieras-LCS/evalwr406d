<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VehiculeRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['vehicule:read']],
    denormalizationContext: ['groups' => ['vehicule:write']],
    operations: [
        new GetCollection(),
        new Get(),
        new Post(),
        new Put(),
        new Patch(),
        new Delete(),
    ]
)]
class Vehicule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['vehicule:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank]
    #[Groups(['vehicule:read', 'vehicule:write', 'marque:read'])]
    private ?string $modele = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Groups(['vehicule:read', 'vehicule:write', 'marque:read'])]
    private ?string $prix = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Groups(['vehicule:read', 'vehicule:write', 'marque:read'])]
    private ?int $puissance = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Range(min: 1886, max: 2100)]
    #[Groups(['vehicule:read', 'vehicule:write', 'marque:read'])]
    private ?int $annee = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['vehicule:read', 'vehicule:write', 'marque:read'])]
    private ?string $photo = null;

    #[ORM\ManyToOne(inversedBy: 'vehicules')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['vehicule:read', 'vehicule:write'])]
    #[\ApiPlatform\Metadata\ApiProperty(readableLink: true, writableLink: false)]
    private ?Marque $marque = null;

    public function getId(): ?int { return $this->id; }

    public function getModele(): ?string { return $this->modele; }
    public function setModele(string $modele): static { $this->modele = $modele; return $this; }

    public function getPrix(): ?string { return $this->prix; }
    public function setPrix(string $prix): static { $this->prix = $prix; return $this; }

    public function getPuissance(): ?int { return $this->puissance; }
    public function setPuissance(int $puissance): static { $this->puissance = $puissance; return $this; }

    public function getAnnee(): ?int { return $this->annee; }
    public function setAnnee(int $annee): static { $this->annee = $annee; return $this; }

    public function getPhoto(): ?string { return $this->photo; }
    public function setPhoto(?string $photo): static { $this->photo = $photo; return $this; }

    public function getMarque(): ?Marque { return $this->marque; }
    public function setMarque(?Marque $marque): static { $this->marque = $marque; return $this; }
}
