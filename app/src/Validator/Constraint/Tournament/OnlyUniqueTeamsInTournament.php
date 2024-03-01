<?php

namespace App\Validator\Constraint\Tournament;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class OnlyUniqueTeamsInTournament extends Constraint
{
    public string $message = 'Команды не могут повторяться';
}
