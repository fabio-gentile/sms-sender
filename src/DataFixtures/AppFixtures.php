<?php

namespace App\DataFixtures;

use App\Entity\Sms;
use App\Entity\SmsReference;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use libphonenumber\PhoneNumber;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use libphonenumber\PhoneNumberUtil;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Création de 100 utilisateurs
        for ($i = 0; $i < 100; $i++) {
            $this->createUser($faker, $manager);
        }

        $manager->flush();

        // Création de 10 messages
        for ($i = 0; $i < 10; $i++) {
            $sms = $this->createSms($faker, $manager);

            // Création d'au moins 50 références SMS pour chaque message
            for ($j = 0; $j < 50; $j++) {
                $user = $this->getRandomUser($manager);
                $this->createSmsReference($faker, $manager, $user, $sms);
            }
        }

        $manager->flush();
    }

    public function createUser(Generator $faker, ObjectManager $manager): void
    {
        $user = new User();
        $phoneNumberUtil = PhoneNumberUtil::getInstance();
        $firstname = $faker->firstName;
        $lastname = $faker->lastName;

        $phoneNumber = new PhoneNumber();
        $countryCode = $faker->randomElement([32, 33, 44, 49, 39, 34]);
        $countryIso = $phoneNumberUtil->getRegionCodeForCountryCode($countryCode);
        $language = $faker->randomElement(['it', 'fr', 'nl', 'en', 'es']);
        $phoneNumber->setCountryCode($countryCode)
            ->setNationalNumber($faker->numberBetween(100000000, 999999999));
        $user->setEmail($faker->email)
            ->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    'password'
                )
            )
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setCountry($countryIso)
            ->setLanguage($language)
            ->setPhoneNumber($phoneNumber);

        $manager->persist($user);
    }

    public function createSms(Generator $faker, ObjectManager $manager): Sms
    {
        $sms = new Sms();
        $sms->setContent($faker->text(160))
            ->setLanguage($faker->randomElement(['it', 'fr', 'nl', 'en', 'es', 'auto']))
            ->setSentAt($faker->dateTimeBetween('-2 month', '1 month'));

        $manager->persist($sms);

        return $sms;
    }

    public function createSmsReference(Generator $faker, ObjectManager $manager, User $user, Sms $sms): void
    {
        $smsReference = new SmsReference();
        $smsReference->setUser($user)
            ->setSms($sms)
            ->setStatus($faker->randomElement(['SENT', 'CANCELLED', 'WAITING']));

        $manager->persist($smsReference);
    }

    public function getRandomUser(ObjectManager $manager): ?User
    {
        $userRepository = $manager->getRepository(User::class);
        $users = $userRepository->findAll();
        return $users[array_rand($users)];
    }
}