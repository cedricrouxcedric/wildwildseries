<?php


namespace App\DataFixtures;


use App\Entity\Episode;
use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 500; $i++) {
            $episode = new Episode();
            $episode->setTitle($faker->sentence);
            $episode->setNumber(rand(0, 100));
            $episode->setSynopsis($faker->sentence);
            $slugify = new Slugify();
            $episode->setSlug($slugify->generate($episode->getTitle()));
            $episode->setSeason($this->getReference('season_' . rand(1, 49)));
            $manager->persist($episode);
            $this->addReference('episode_' . $i, $episode);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [SeasonFixtures::class];
    }
}
