<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Actor;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $actor = new Actor();
        $actor->setName("ossama");
        $manager->persist($actor);

         $actor2 = new Actor();
        $actor2->setName("amine");
        $manager->persist($actor2);

          $actor3 = new Actor();
        $actor3->setName("ahmed");
        $manager->persist($actor3);

        $manager->flush();

        $this->addReference("actor_1", $actor);
        $this->addReference("actor_2", $actor2);
        $this->addReference("actor_3", $actor3);
        }
}