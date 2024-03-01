<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'games')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tournament $tournament = null;

    #[ORM\ManyToOne(inversedBy: 'homeGames')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Team $leftTeam = null;

    #[ORM\ManyToOne(inversedBy: 'guestGames')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Team $rightTeam = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $date = null;

    public function __construct()
    {
        $this->teams = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTournament(): ?Tournament
    {
        return $this->tournament;
    }

    public function setTournament(?Tournament $tournament): static
    {
        $this->tournament = $tournament;

        return $this;
    }

    public function getLeftTeam(): ?Team
    {
        return $this->leftTeam;
    }

    public function setLeftTeam(?Team $leftTeam): static
    {
        $this->leftTeam = $leftTeam;

        return $this;
    }

    public function getRightTeam(): ?Team
    {
        return $this->rightTeam;
    }

    public function setRightTeam(?Team $rightTeam): static
    {
        $this->rightTeam = $rightTeam;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(?\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }
}
