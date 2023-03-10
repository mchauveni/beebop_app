<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Admin;
use App\Entity\Beekeeper;
use App\Entity\Apiary;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;


class AppFixtures extends Fixture
{
    const MAX_BEEKEEPERS = 5;

    const MAX_APIARIES = 8;
    public function load(ObjectManager $manager)
    {
        $this->loadAdmin($manager);
        $this->loadBeekeeper($manager);
    }
    public function loadBeekeeper(ObjectManager $manager)
    {
        $faker = Factory::create();
        $apiaries = [];

        // création des apiculteurs
        for ($i = 0; $i < self::MAX_BEEKEEPERS; $i++) {
            $beekeeper = new Beekeeper();
            $beekeeper
                ->setLastName($faker->lastName())
                ->setFirstName($faker->firstName())
                ->setLogin($faker->username())
                ->setMail($faker->email())
                ->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeInInterval('-20 days', '+10 days')))
                ->setVerified(false)
                ->setPassword($faker->password());
            // création des ruchers
            for ($j = 0; $j < self::MAX_APIARIES; $j++) {
                $apiary = new Apiary();
                $apiary
                    ->setName($faker->word())
                    ->setZipCode((int)$faker->postcode())
                    ->setLocalisation($faker->address());
                $manager->persist($apiary);
                $apiaries[] = $apiary;
            }

            // attribution des ruchers de manière aléatoire
            $randomApiaries = $faker->randomElements($apiaries, $faker->numberBetween(1, 4));
            foreach ($randomApiaries as $apiary) {
                $beekeeper->addApiary($apiary);
            }

            $manager->persist($beekeeper);
        }


        $manager->flush();
    }
    public function loadAdmin(ObjectManager $manager)
    {
        // initialisation de faker pour créer de fausses données administrateur
        $faker = Factory::create();
        // création d'un nouvel admin
        $admin = new Admin();
        $admin
            ->setLogin($faker->username())
            ->setMail($faker->email())
            ->setPassword($faker->password());
        // persistance de l'entity
        $manager->persist($admin);
        // envoie de l'entity
        $manager->flush();
    }
}