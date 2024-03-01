<?php

namespace App\Utils;

use App\Entity\Game;
use App\Entity\Team;
use App\Entity\Tournament;
use Doctrine\ORM\EntityManagerInterface;

class TournamentService
{
    public function __construct(private ScheduleGeneratorService $scheduleGeneratorService,
        private EntityManagerInterface $entityManager)
    {
    }

    public function generateSchedule(Tournament $tournament): array
    {
        $teamIds = [];
        foreach ($tournament->getTeams() as $team) {
            $teamIds[$team->getId()] = $team->getId();
        }
        $matchesPerDay = $this->scheduleGeneratorService->generateSchedule($teamIds);
        $date = new \DateTimeImmutable();
        foreach ($matchesPerDay as $dayWithMatches) {
            foreach ($dayWithMatches as $match) {
                $game = new Game();
                $game->setLeftTeam($this->entityManager->getReference(Team::class, $match[0]));
                $game->setRightTeam($this->entityManager->getReference(Team::class, $match[1]));
                $game->setDate($date);
                $game->setTournament($tournament);
                $this->entityManager->persist($game);
            }
            $date = $date->modify('+1 day');
        }

        return $matchesPerDay;
    }

    public function regenerateSchedule(Tournament $tournament)
    {
        $tournament->getGames()->clear();

        return $this->generateSchedule($tournament);
    }
}
