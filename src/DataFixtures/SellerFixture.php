<?php

namespace App\DataFixtures;

use App\Entity\Seller;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class SellerFixture extends Fixture
{

    const SELLER_COUNT = 20;

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) : void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($s = 0; $s < 20; $s++) {
            $seller = new Seller();

            $seller->setFirstName($faker->firstName);
            $seller->setLastName($faker->lastName);
            $seller->setEmail($faker->email);
            $seller->setPhone($faker->phoneNumber);
            $seller->setAddress($faker->address);
            $seller->setCompany($faker->company);
            $seller->setSiret($faker->numerify('##############'));
            $seller->setVat($faker->numerify('FR###########'));
            $seller->setPassword($this->passwordHasher->hashPassword($seller, 'password'));
            $seller->setSellerRating($faker->randomFloat(1, 0, 5));
            $seller->setRoles(['ROLE_COMPANY']);

            $this->addReference('seller_' . $s, $seller);
            $manager->persist($seller);

        }

        $manager->flush();
    }
}