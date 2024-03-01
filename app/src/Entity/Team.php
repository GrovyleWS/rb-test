<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 3, max: 30, minMessage: 'Имя не может содержать менее 3 символов', maxMessage: 'Имя не может содержать больше 30 символов')]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Tournament::class, mappedBy: 'teams')]
    private Collection $tournaments;

    #[ORM\OneToMany(targetEntity: Game::class, mappedBy: 'leftTeam')]
    private Collection $homeGames;
    #[ORM\OneToMany(targetEntity: Game::class, mappedBy: 'rightTeam')]
    private Collection $guestGames;

    private Collection $games;

    public function __construct()
    {
        $this->tournaments = new ArrayCollection();
        $this->games = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Tournament>
     */
    public function getTournaments(): Collection
    {
        return $this->tournaments;
    }

    public function addTournament(Tournament $tournament): static
    {
        if (!$this->tournaments->contains($tournament)) {
            $this->tournaments->add($tournament);
            $tournament->addTeam($this);
        }

        return $this;
    }

    public function removeTournament(Tournament $tournament): static
    {
        if ($this->tournaments->removeElement($tournament)) {
            $tournament->removeTeam($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addHomeGame(Game $game): static
    {
        if (!$this->games->contains($game)) {
            $this->games->add($game);
            $game->setLeftTeam($this);
        }

        return $this;
    }

    public function removeGame(Game $game): static
    {
        if ($this->games->removeElement($game)) {
            // set the owning side to null (unless already changed)
            if ($game->getLeftTeam() === $this) {
                $game->setLeftTeam(null);
            }
        }

        return $this;
    }

    public function addGuestGame(Game $game): static
    {
        if (!$this->games->contains($game)) {
            $this->games->add($game);
            $game->setRightTeam($this);
        }

        return $this;
    }

    public function removeGuestGame(Game $game): static
    {
        if ($this->games->removeElement($game)) {
            // set the owning side to null (unless already changed)
            if ($game->getRightTeam() === $this) {
                $game->setRightTeam(null);
            }
        }

        return $this;
    }
}
