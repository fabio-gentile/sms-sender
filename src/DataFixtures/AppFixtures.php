<?php

namespace App\DataFixtures;

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
        $slugger = new AsciiSlugger();
        $faker = Factory::create('fr_FR');

        $this->createUser($faker, $slugger, $manager, true);

        for ($i = 0; $i < 10; $i++) {
            $this->createUser($faker, $slugger, $manager);
        }

        $manager->flush();
    }

    /**
     * @param Generator $faker
     * @param AsciiSlugger $slugger
     * @param ObjectManager $manager
     * @return void
     */
    public function createUser(Generator $faker, AsciiSlugger $slugger, ObjectManager $manager,bool $admin = false): void
    {
        $user = new User();
        $phoneNumberUtil = PhoneNumberUtil::getInstance();
        $firstname = $faker->firstName;
        $lastname = $faker->lastName;

        $phoneNumber = new PhoneNumber();
        $countryCode = $faker->randomElement([32, 33, 44, 49, 39, 34]);
        $countryIso = $phoneNumberUtil->getRegionCodeForCountryCode($countryCode);
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
            ->setSlug($slugger->slug(strtolower($firstname) . ' ' . strtolower($lastname) . ' ' . uniqid()))
            ->setPhoneNumber($phoneNumber);

        if ($admin) {
            $user->setRoles(['ROLE_ADMIN']);
        }

        $manager->persist($user);
    }
}
