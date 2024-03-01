<?php

namespace App\Utils;

class ScheduleGeneratorService
{
    public function generateSchedule(array $teamIds, $maxMatchesPerDay = 4, $maxMatchesTeamCanPlayPerDay = 1)
    {
        $numberOfTeams = count($teamIds);
        if ($numberOfTeams < 2) {
            // кастомный класс сделать
            throw new \Exception('В турнире должно принимать участие от 2 команд');
        }
        $pullOfMatches = [];
        $daysMatches = [];
        foreach ($teamIds as $id) {
            $pullOfMatches[$id] = [];
        }

        // pullOfMatches - весь список возможных матчей для каждой команды в случайном порядке
        foreach ($teamIds as $teamId => $team) {
            $pullOfOpponents = $teamIds;
            unset($pullOfOpponents[$teamId]);
            while (0 !== count($pullOfOpponents)) {
                $opponentId = $pullOfOpponents[array_rand($pullOfOpponents)];
                $pullOfMatches[$teamId][$opponentId] = $opponentId;
                unset($pullOfOpponents[$opponentId]);
            }
        }
        $pullOfTeams = $teamIds;
        $matches = [];

        // Теперь для каждой команды ставим матчи из pullOfMatches в расписание по одному с учётом условий
        // Когда матч поставлен в расписание, он удаляется из pullOfMatches
        // Когда pullPfMatches становится пустым - распиание cформировано, выдать результат
        while (count($pullOfMatches) > 0) {
            $day = 1;
            // Начинаем работу алгоритма со случайной команды из pullOfMatches
            $teamId = array_rand($pullOfTeams);
            // Пока для команды не расставлены в раcписаниb все матчи
            while (isset($pullOfMatches[$teamId])) {
                // определяем противника
                $opponentId = array_shift($pullOfMatches[$teamId]);
                $dayOfMatchWithOpponent = $day;
                // ищем день свободный для обеих команд
                while (isset($matches[$teamId][$dayOfMatchWithOpponent])
                    && count($matches[$teamId][$dayOfMatchWithOpponent]) === $maxMatchesTeamCanPlayPerDay
                    || isset($daysMatches[$dayOfMatchWithOpponent])
                    && count($daysMatches[$dayOfMatchWithOpponent]) === $maxMatchesPerDay
                    || isset($matches[$opponentId][$dayOfMatchWithOpponent])
                    && count($matches[$opponentId][$dayOfMatchWithOpponent]) === $maxMatchesTeamCanPlayPerDay) {
                    // если день не подходит, смотрим следующий
                    ++$dayOfMatchWithOpponent;
                }
                // ставим матчи в расписание и удаляем из pullOfMatches
                $daysMatches[$dayOfMatchWithOpponent][] = [$teamId, $opponentId];
                $matches[$teamId][$dayOfMatchWithOpponent][] = $opponentId;
                $matches[$opponentId][$dayOfMatchWithOpponent][] = $teamId;
                unset($pullOfMatches[$opponentId][$teamId], $pullOfMatches[$teamId][$opponentId]);

                // если в изначальном дне команда больше не сможет играть ещё матч, смотрим следующий день
                if (isset($matches[$teamId][$day])
                    && count($matches[$teamId][$day]) === $maxMatchesTeamCanPlayPerDay
                    || isset($daysMatches[$day])
                    && count($daysMatches[$day]) === $maxMatchesPerDay) {
                    ++$day;
                }
                // Если все матчи расставлены, удаляем данные о команде
                if (0 === count($pullOfMatches[$teamId])) {
                    unset($pullOfMatches[$teamId]);
                    unset($pullOfTeams[$teamId]);
                }
                if (0 === count($pullOfMatches[$opponentId])) {
                    unset($pullOfMatches[$opponentId]);
                    unset($pullOfTeams[$opponentId]);
                }
            }
        }
        // Обеспечиваем случайный порядок матчей
        foreach ($daysMatches as &$day) {
            shuffle($day);
        }

        return $daysMatches;
    }
}
