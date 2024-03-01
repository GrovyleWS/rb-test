<?php

namespace App\Entity;

use App\Repository\TournamentRepository;
use App\Validator\Constraint\Tournament as CustomConstraint;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TournamentRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'Турнир с таким именем уже существует')]
class Tournament
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 3, max: 30, minMessage: 'Имя не может содержать менее 3 символов', maxMessage: 'Имя не может содержать больше 30 символов')]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Team::class, inversedBy: 'tournaments', cascade: ['persist'], fetch: 'EAGER', indexBy: 'id')]
    #[CustomConstraint\OnlyUniqueTeamsInTournament]
    private Collection $teams;

    #[ORM\OneToMany(targetEntity: Game::class, mappedBy: 'tournament', cascade: ['persist'], orphanRemoval: true)]
    private Collection $games;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    public function __construct()
    {
        $this->teams = new ArrayCollection();
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
     * @return Collection<int, Team>
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): static
    {
        if (!$this->teams->contains($team)) {
            $this->teams->add($team);
        }

        return $this;
    }

    public function addTeams(iterable $teams)
    {
        /**
         * @var Team $team
         */
        foreach ($teams as $team) {
            $this->addTeam($team);
        }

        return $this;
    }

    public function removeTeam(Team $team): static
    {
        $this->teams->removeElement($team);

        return $this;
    }

    public function removeTeams(iterable $teams)
    {
        /**
         * @var Team $team
         */
        foreach ($teams as $team) {
            $this->removeTeam($team);
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

    public function addGame(Game $game): static
    {
        if (!$this->games->contains($game)) {
            $this->games->add($game);
            $game->setTournament($this);
        }

        return $this;
    }

    public function removeGame(Game $game): static
    {
        if ($this->games->removeElement($game)) {
            // set the owning side to null (unless already changed)
            if ($game->getTournament() === $this) {
                $game->setTournament(null);
            }
        }

        return $this;
    }

    public function removeGames(iterable $games)
    {
        /**
         * @var Game $game
         */
        foreach ($games as $game) {
            $this->removeGame($game);
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getGamesIndexedById()
    {
        $indexedGames = new ArrayCollection();

        foreach ($this->getGames() as $game) {
            $gameDate = $game->getDate()->format('Y-m-d');
            if (!$indexedGames->containsKey($gameDate)) {
                $indexedGames->set($gameDate, new ArrayCollection());
            }
            $indexedGames[$gameDate]->add($game);
        }

        return $indexedGames;
    }

    public function getTeamsIdArray()
    {
        return $this->getTeams()->map(function ($team) {
            return $team->getId();
        })->toArray();
    }
}
