<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Movie;

class MoviesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $movie = new Movie();
        $movie->setTitle('Walking Dead');
        $movie->setReleaseYear(2009);
        $movie->setDescription('Film Zombie Walking Dead');
        $movie->setImagePath('https://cdn.pixabay.com/photo/2016/09/13/07/35/walking-dead-1666584_1280.jpg');
        $movie->addActor($this->getReference('actor_1'));
        $movie->addActor($this->getReference('actor_2'));
        $manager->persist($movie);
        
         $movie2 = new Movie();
        $movie2->setTitle('Breaking bad');
        $movie2->setReleaseYear(2019);
        $movie2->setDescription('Film Breaking bad');
        $movie2->setImagePath('https://cdn.pixabay.com/photo/2018/10/26/07/30/road-3774012_1280.jpg');
        $movie2->addActor($this->getReference('actor_3'));
        $movie2->addActor($this->getReference('actor_2'));
        $manager->persist($movie2);

        $manager->flush();
    }
}