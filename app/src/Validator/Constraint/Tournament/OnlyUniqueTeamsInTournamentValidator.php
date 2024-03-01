<?php

namespace App\Validator\Constraint\Tournament;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class OnlyUniqueTeamsInTournamentValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof OnlyUniqueTeamsInTournament) {
            throw new UnexpectedValueException($constraint, OnlyUniqueTeamsInTournament::class);
        }
        if (!$value instanceof Collection) {
            throw new UnexpectedValueException($value, Collection::class);
        }
        $teams = $value;
        $checked = [];
        foreach ($teams as $team) {
            if (in_array($team->getId(), $checked)) {
                $this->context->buildViolation($constraint->message)->addViolation();

                return;
            }
            $checked[] = $team->getId();
        }
    }
}
