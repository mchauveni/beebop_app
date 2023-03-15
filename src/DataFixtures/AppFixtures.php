<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Task;
use App\Entity\Admin;
use App\Entity\Apiary;
use DateTimeImmutable;
use App\Entity\Beehive;
use App\Entity\Product;
use App\Entity\Beekeeper;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    private $passwordHasher;
    // injecter le service de cryptage
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    const MAX_BEEKEEPERS = 5;

    const MAX_APIARIES = 8;

    const MAX_BEEHIVES = 25;

    const MAX_TASKS = 6;

    const MAX_PRODUCTS = 10;

    const PRODUCTS = ["miel", "cire", "pollen"];

    const RACE_BEES = ["noire", "caucasienne", "carnica", "italienne", "buckfast"];

    public function load(ObjectManager $manager)
    {
        $this->loadAdmin($manager);
        $this->loadBeekeeper($manager);
    }
    public function loadBeekeeper(ObjectManager $manager)
    {
        $faker = Factory::create();

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

            $manager->persist($beekeeper);
            $this->addReference('Beekeeper-' . ($i), $beekeeper);
        }
        // création des ruchers
        for ($j = 0; $j < self::MAX_APIARIES; $j++) {
            $apiary = new Apiary();
            $apiary
                ->setName($faker->word())
                ->setZipCode((int)$faker->postcode())
                ->setLocalisation($faker->address())
                ->setBeekeeper($this->getReference('Beekeeper-' . random_int(0, self::MAX_BEEKEEPERS - 1)));
            $this->addReference('Apiary-' . ($j), $apiary);
            $manager->persist($apiary);
        }
        // création des ruches
        for ($k = 0; $k < self::MAX_BEEHIVES; $k++) {
            $beehive = new Beehive();
            $beehive
                ->setName($faker->word())
                ->setRace($faker->randomElement(self::RACE_BEES))
                ->setApiary($this->getReference('Apiary-' . random_int(0, self::MAX_APIARIES - 1)));

            $manager->persist($beehive);
            $this->addReference('Beehive-' . ($k), $beehive);
        }

        // création des tâches
        for ($l = 0; $l < self::MAX_TASKS; $l++) {
            $task = new Task();
            $task
                ->setType($faker->word())
                ->setDescription($faker->paragraph(3, true))
                ->setDoAt(DateTimeImmutable::createFromMutable($faker->dateTimeInInterval('-20 days', '+10 days')))
                ->setBeehive($this->getReference('Beehive-' . random_int(0, self::MAX_BEEHIVES - 1)));

            $manager->persist($task);
        }

        // création des produits
        for ($m = 0; $m < self::MAX_PRODUCTS; $m++) {
            $product = new Product();
            $product
                ->setType($faker->randomElement(self::PRODUCTS))
                ->setDate(DateTimeImmutable::createFromMutable($faker->dateTimeInInterval('-20 days', '+10 days')))
                ->setQuantity($faker->randomDigitNotNull())
                ->setBeehive($this->getReference('Beehive-' . random_int(0, self::MAX_BEEHIVES - 1)));

            $manager->persist($product);
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
            ->setLogin("test")
            ->setMail($faker->email())
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->passwordHasher->hashPassword($admin, 'test'));
        // persistence de l'entity
        $manager->persist($admin);
        // envoie de l'entity
        $manager->flush();
    }
}
