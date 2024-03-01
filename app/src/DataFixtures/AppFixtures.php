<?php

namespace App\DataFixtures;

use App\Entity\Team;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 12; ++$i) {
            $team = new Team();
            $team->setName("Команда $i");
            $manager->persist($team);
        }

        $manager->flush();
    }
}
