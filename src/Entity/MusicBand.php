<?php

namespace App\Entity;

use App\Repository\MusicBandRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MusicBandRepository::class)]
#[ORM\Table(name: 'music_bands')]
class MusicBand
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: City::class, fetch: 'EAGER', inversedBy: 'musicBands')]
    #[ORM\JoinColumn(name: 'id_city')]
    private City $city;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $id_city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $genre = null;

    #[ORM\Column(nullable: true)]
    private ?int $members = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $start_year = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $end_year = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $founder = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getIdCity(): ?int
    {
        return $this->id_city;
    }

    public function setIdCity(int $id_city): static
    {
        $this->id_city = $id_city;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(?string $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function getMembers(): ?int
    {
        return $this->members;
    }

    public function setMembers(?int $members): static
    {
        $this->members = $members;

        return $this;
    }

    public function getStartYear(): ?int
    {
        return $this->start_year;
    }

    public function setStartYear(int $start_year): static
    {
        $this->start_year = $start_year;

        return $this;
    }

    public function getEndYear(): ?int
    {
        return $this->end_year;
    }

    public function setEndYear(?int $end_year): static
    {
        $this->end_year = $end_year;

        return $this;
    }

    public function getFounder(): ?string
    {
        return $this->founder;
    }

    public function setFounder(?string $founder): static
    {
        $this->founder = $founder;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
