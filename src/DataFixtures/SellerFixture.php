<?php

namespace App\DataFixtures;

use App\Entity\Seller;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class SellerFixture extends Fixture
{

    const SELLER_COUNT = 20;

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
            $seller->setCity($faker->city);
            $seller->setStreet($faker->city);
            $seller->setStreetNumber($faker->city);
            $seller->setPostalCode($faker->city);
            $seller->setCompany($faker->company);
            $seller->setSiret($faker->numerify('##############'));
            $seller->setVat($faker->numerify('FR###########'));
            $seller->setPassword('seller');
            $seller->setRating($faker->randomFloat(1, 0, 5));
            $seller->setRoles(['ROLE_SELLER']);
            $seller->setCreatedAt($faker->dateTimeBetween('- 1 year', 'now'));

            $this->addReference('seller_' . $s, $seller);
            $manager->persist($seller);

        }

        $manager->flush();
    }
}