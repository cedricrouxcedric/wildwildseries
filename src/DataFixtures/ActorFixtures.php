<?php


namespace App\DataFixtures;

use App\Entity\Actor;
use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    const ACTORS = [
        'Andrew Lincoln' => [
            'program' => ["program_0", "program_5"]
        ],
        'Norman Reedus' => [
            'program' => ["program_0"],
        ],
        'Lauren Cohan' => [
            'program' => ["program_0"],
        ],
        'Danai Gurira' => [
            'program' => ["program_0"],
        ],
    ];

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('en_US');
        $i = 0;
        foreach (self::ACTORS as $actorName => $data) {
            $actor = new Actor();

            $actor->setName($actorName);
            $slugify = new Slugify();
            $actor->setSlug($slugify->generate($actor->getName()));
            foreach ($data['program'] as $movie) {
                $actor->addProgram($this->getReference($movie));
            }
            $manager->persist($actor);
            $this->addReference('actor_' . $i, $actor);
            $i++;
        }
        for ($i = 0; $i < 50; $i++) {
            $actor = new Actor();
            $actor->setName($faker->name);
            $slugify = new Slugify();
            $actor->setSlug($slugify->generate($actor->getName()));
            $number = rand(0, 5);
            $actor->addProgram($this->getReference("program_" . $number));
            $manager->persist($actor);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }
}
