<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AdminFixture extends Fixture
{
    /**
     * @param ObjectManager $manager
     * @return void
     *
     */
    public function load(ObjectManager $manager): void
    {
        $user = new Customer();
        $user->setEmail('admin@email.com');
        $user->setFirstName('Admin');
        $user->setLastName('Admin');
        $user->setPassword('admin');
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);

        $manager->flush();

    }
}