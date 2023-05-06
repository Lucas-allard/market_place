<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CustomerFixture extends Fixture
{

    const CUSTOMER_COUNT = 100;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i <= self::CUSTOMER_COUNT; $i++) {
            $customer = new Customer();
            $customer->setFirstName($faker->firstName);
            $customer->setLastName($faker->lastName);
            $customer->setEmail($faker->email);
            $customer->setPhone($faker->phoneNumber);
            $customer->setAddress($faker->address);
            $customer->setBirthDate($faker->dateTime);
            $customer->setShippingAddress($faker->address);
            $customer->setPassword($this->passwordHasher->hashPassword($customer, 'password'));

            $manager->persist($customer);

            $this->addReference('customer_' . $i, $customer);
        }

        $manager->flush();
    }
}